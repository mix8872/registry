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
    protected $signature = 'structure:load-collab-projects';

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
            if (isset($projects['message'])) {
                $this->line($projects['message']);
                return;
            }
            $this->withProgressBar($projects, function ($project) {
                Project::makeFromCollab($project);
            });

            $this->line('Сверка архивных проектов');
            $projects = $client->get('projects/archive')->getJson();

            $this->withProgressBar($projects, function ($project) {
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
