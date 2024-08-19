<?php

namespace App\Classes;

use App\Models\Project;
use App\Models\User;
use Http\Discovery\Exception\NotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class ActiveCollabHooks
{
    public const MSG_ARCHIVED = 'The project has been archived';
    public const MSG_UNARCHIVED = 'The project has been unarchived';
    public const MSG_CREATED = 'The project has been created';

    public const COLLAB_EVENT_CREATED = 'ProjectCreated';
    public const COLLAB_EVENT_DELETED = 'ProjectMovedToTrash';
    public const COLLAB_EVENT_RESTORED = 'ProjectRestoredFromTrash';
    public const COLLAB_EVENT_COMPLETED = 'ProjectCompleted';
    public const COLLAB_EVENT_REOPENED = 'ProjectReopened';

    public static $events = [
        self::COLLAB_EVENT_CREATED => 'Проект создан',
        self::COLLAB_EVENT_DELETED => 'Проект удален',
        self::COLLAB_EVENT_RESTORED => 'Проект восстановлен',
        self::COLLAB_EVENT_COMPLETED => 'Проект архивирован',
        self::COLLAB_EVENT_REOPENED => 'Проект разархивирован'
    ];

    public function __construct(private readonly array $data)
    {
    }

    /**
     * Отработка события создания проекта
     *
     * @return JsonResponse
     */
    public function projectCreated(): JsonResponse
    {
        Project::makeFromCollab($this->data['payload']);
        return response()->json([
            'success' => true,
            'message' => self::MSG_CREATED
        ]);
    }

    /**
     * Отработка события удаления проекта
     *
     * @return JsonResponse
     */
    public function projectMovedToTrash()
    {
        self::getProjectById($this->data['payload']['id'])->archive(self::getCollabUserById($this->data['payload']['updated_by_id']));
        return response()->json([
            'success' => true,
            'message' => self::MSG_ARCHIVED
        ]);
    }

    /**
     * Отработка события восстановления проекта
     *
     * @return JsonResponse
     */
    public function projectRestoredFromTrash()
    {
        self::getProjectById($this->data['payload']['id'])->unarchive(self::getCollabUserById($this->data['payload']['updated_by_id']));
        return response()->json([
            'success' => true,
            'message' => self::MSG_UNARCHIVED
        ]);
    }

    /**
     * Отработка события закрытия проекта
     *
     * @return JsonResponse
     */
    public function projectCompleted()
    {
        self::getProjectById($this->data['payload']['id'])->archive(self::getCollabUserById($this->data['payload']['updated_by_id']));
        return response()->json([
            'success' => true,
            'message' => self::MSG_ARCHIVED
        ]);
    }

    /**
     * Отработка события открытия проекта
     *
     * @return JsonResponse
     */
    public function projectReopened()
    {
        self::getProjectById($this->data['payload']['id'])->unarchive(self::getCollabUserById($this->data['payload']['updated_by_id']));
        return response()->json([
            'success' => true,
            'message' => self::MSG_UNARCHIVED
        ]);
    }

    /**
     * Получение модели локального проекта по id проекта из ActiveCollab
     *
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
     * Получение локального пользователя по id из ActiveCollab
     *
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
