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
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Modules\Finance\Models\FinanceEconomy;
use Modules\Finance\Models\FinanceSpentFact;

class ImportSpentFacts implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Экземпляр проекта.
     *
     * @var \App\Models\Project|null
     */
    public Project|null $project;

    /**
     * Экземпляр отчета.
     *
     * @var FinanceEconomy|null
     */
    public FinanceEconomy|null $economy;

    /**
     */
    public function uniqueId(): string
    {
        return $this->project->id;
    }

    /**
     * Create a new job instance.
     */
    public function __construct($projectId, $economyId)
    {
        switch (true) {
            case !$this->project = Project::find($projectId):
                throw new \Exception("Проект c id {$projectId} не найден");
            case !$this->economy = FinanceEconomy::find($economyId):
                throw new \Exception("Отчеты c id {$economyId} не найден");
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->economy->setStatus(FinanceEconomy::STATUS_PROCESS);

            $client = ActiveCollabClient::make();

            $page = 1;
            $records = [];
            do {
                $r = $client->get("projects/{$this->project->crm_id}/time-records/?page=$page")->getJson();
                if (!$r) {
                    continue;
                }
                $records[] = $r;
                $page++;
            } while ($r && $r['time_records']);
            $records = array_merge_recursive(...$records);

            switch (true) {
                case !$records:
                    throw new \Exception('Записи о затреканном времени не получены');
                case isset($records['message']):
                    throw new \Exception($records['message']);
            }
            FinanceSpentFact::makeFromCollab($this->project, $records['time_records'], $records['related']['Task']);

            $facts = $this->economy->facts->groupBy('finance_res_id');
            $rates = $this->economy->rates;
            $this->economy->spents()->delete();
            $spents = [];
            foreach ($rates as $rate) {
                if (!isset($facts[$rate['id']])) {
                    continue;
                }

                $spentCount = round($facts[$rate['id']]->sum('count'));
                $soldCount = round($rate['sold']);
                $priceIn = round($spentCount / 3600 * $rate['in'], 2);
                $priceOut = round($soldCount * $rate['out'], 2);

                if (!$priceOut) {
                    continue;
                }

                $spents[] = [
                    'finance_res_id' => $rate['id'],
                    'sold_count' => $soldCount,
                    'spent_count' => $spentCount,
                    'rate_in' => $rate['in'],
                    'rate_out' => $rate['out'],
                    'price_in' => $priceIn,
                    'price_out' => $priceOut,
                    'performance' => round(($priceOut - $priceIn) / $priceOut, 2),
                    'profit' => $priceOut - $priceIn
                ];
            }
            $this->economy->spents()->createMany($spents);

            $performances = Arr::pluck($spents, 'performance');
            $profits = Arr::pluck($spents, 'profit');
            $this->economy->performance = array_sum($performances);
            $this->economy->profit = array_sum($profits);

            $this->economy->setStatus(FinanceEconomy::STATUS_DONE);
        } catch (\Error|\Exception $e) {
            $this->economy->setStatus(FinanceEconomy::STATUS_ERROR, $e->getMessage());
            Log::error($e->getMessage());
            if (config('app.debug')) {
                Log::error($e->getTraceAsString());
            }
        }
    }
}
