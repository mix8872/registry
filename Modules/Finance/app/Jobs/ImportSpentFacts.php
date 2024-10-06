<?php

namespace Modules\Finance\Jobs;

use App\Classes\ActiveCollabClient;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Finance\Models\FinanceSpentFact;

class ImportSpentFacts implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Экземпляр продукта.
     *
     * @var \App\Models\Project
     */
    public $project;

    /**
     */
    public function uniqueId(): string
    {
        return $this->project->id;
    }

    /**
     * Create a new job instance.
     */
    public function __construct($projectId)
    {
        if (!$this->project = Project::find($projectId)) {
            throw new \Exception("Проект c id {$projectId} не найден");
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $client = ActiveCollabClient::make();

            $records = $client->get("projects/{$this->project->crm_id}/time-records")->getJson();
            if (isset($records['message'])) {
                $this->line($records['message']);
                return;
            }
            FinanceSpentFact::makeFromCollab($records->time_records, $records->related->Task);

        } catch (\Error|\Exception $e) {
            $this->error($e->getMessage());
            if (config('app.debug')) {
                $this->error($e->getTraceAsString());
            }
        }
    }
}
