<?php

namespace App\Classes;

use App\Models\Project;
use App\Models\User;
use Http\Discovery\Exception\NotFoundException;
use Illuminate\Support\Facades\Cache;

class ActiveCollabHooks
{
    public const COLLAB_EVENT_DELETED = 'ProjectMovedToTrash';
    public const COLLAB_EVENT_RESTORED = 'ProjectRestoredFromTrash';
    public const COLLAB_EVENT_COMPLETED = 'ProjectCompleted';
    public const COLLAB_EVENT_REOPENED = 'ProjectReopened';

    public static $events = [
        self::COLLAB_EVENT_DELETED => 'Проект удален',
        self::COLLAB_EVENT_RESTORED => 'Проект восстановлен',
        self::COLLAB_EVENT_COMPLETED => 'Проект архивирован',
        self::COLLAB_EVENT_REOPENED => 'Проект разархивирован'
    ];

    public function __construct(private readonly array $data)
    {
    }

    public function projectMovedToTrash()
    {
        self::getProjectById($this->data['payload']['id'])->archive(self::getCollabUserById($this->data['payload']['updated_by_id']));
        return response()->json([
            'success' => true,
            'message' => 'Project was archived'
        ]);
    }

    public function projectRestoredFromTrash()
    {
        self::getProjectById($this->data['payload']['id'])->unarchive(self::getCollabUserById($this->data['payload']['updated_by_id']));
        return response()->json([
            'success' => true,
            'message' => 'Project was unarchived'
        ]);
    }

    public function projectCompleted()
    {
        self::getProjectById($this->data['payload']['id'])->archive(self::getCollabUserById($this->data['payload']['updated_by_id']));
        return response()->json([
            'success' => true,
            'message' => 'Project was archived'
        ]);
    }

    public function projectReopened()
    {
        self::getProjectById($this->data['payload']['id'])->unarchive(self::getCollabUserById($this->data['payload']['updated_by_id']));
        return response()->json([
            'success' => true,
            'message' => 'Project was unarchived'
        ]);
    }

    /**
     * @param int $id
     * @return Project
     */
    private static function getProjectById(int $id): Project
    {
        if (!$project = Project::firstWhere('crm_id', $id)) {
            throw new NotFoundException('Project not found', 404);
        }
        return $project;
    }

    /**
     * @param int $id
     * @return mixed
     */
    private static function getCollabUserById(int $id)
    {
        $user = Cache::remember("collab-user-$id", 86400, function () use ($id) {
            $client = ActiveCollabClient::make();
            $data = $client->get("users/$id")->getJson();
            return $data['single'] ?? null;
        });
        $name = substr($user['email'], 0, strpos($user['email'], '@'));
        return User::firstWhere('name', $name);
    }
}
