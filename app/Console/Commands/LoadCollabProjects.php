<?php

namespace App\Console\Commands;

use ActiveCollab\SDK\Token;
use App\Classes\ActiveCollabClient;
use App\Models\Project;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoadCollabProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:load-collab-projects';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Импорт/обновление проектов из ActiveCollab';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $client = ActiveCollabClient::make();
            $this->line('Начало импорта проектов');
            $this->line('Получение проектов');
            $projects = $client->get('projects')->getJson();
            $this->withProgressBar($projects, function ($project) use ($host) {
                $user = User::firstOrCreate(['email' => $project['created_by_email']], [
                    'name' => substr($project['created_by_email'], 0, strpos($project['created_by_email'], '@')),
                    'email' => $project['created_by_email'],
                    'password' => Hash::make($project['created_by_email'])
                ]);
                $attributes = [
                    'name' => $project['name'],
                    'comment' => $project['body'],
                    'status' => $project['is_trashed'] ? Project::STATUS_ARCHIVED : Project::STATUS_ACTIVE,
                    'crm_id' => $project['id'],
                    'crm_url' => "{$host}{$project['url_path']}",
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                    'created_at' => date('Y-m-d H:i:s', $project['created_on']),
                ];
                $project = Project::firstOrNew(['crm_id' => $project['id']], $attributes);
                if ($project->exists) {
                    $project->update($attributes);
                }
                $project->save();
            });

            $this->line('Сверка архивных проектов');
            $projects = $client->get('projects/archive')->getJson();
            $this->withProgressBar($projects, function ($project) use ($host) {
                if ($project = Project::firstWhere(['crm_id' => $project['id']])) {
                    $project->status = Project::STATUS_ARCHIVED;
                    $project->save();
                }
            });

            $this->newLine()->line('Импорт завершен');
        } catch (\Error|\Exception $e) {
            $this->error($e->getMessage());
            if (config('app.debug')) {
                $this->error($e->getTraceAsString());
            }
        }
    }
}
