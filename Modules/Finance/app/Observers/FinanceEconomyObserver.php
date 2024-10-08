<?php

namespace Modules\Finance\Observers;

use App\Classes\ActiveCollabClient;
use App\Models\Project;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Modules\Finance\Jobs\ImportSpentFacts;
use Modules\Finance\Models\FinanceEconomy;
use Modules\Finance\Models\FinanceEconomySpent;
use Modules\Finance\Models\FinanceSpentFact;

class FinanceEconomyObserver
{
    public function saved(Model $r)
    {
        try {
            if (!in_array($r->status, [FinanceEconomy::STATUS_PROCESS])) {
                ImportSpentFacts::dispatch($r->project_id, $r->id);
            }

            /*$r->spents()->delete();

            if (!$project = Project::find($r->project_id)) {
                throw new \Exception("Проект c id {$r->id} не найден");
            }

            $client = ActiveCollabClient::make();

            $page = 1;
            $records = [];
            do {
                $r = $client->get("projects/{$project->crm_id}/time-records/?page=$page")->getJson();
                $records[] = $r;
                $page++;
            } while ($r['time_records']);
            $records = array_merge_recursive(...$records);

            switch (true) {
                case !$records:
                    throw new \Exception('Записи о затреканном времени не получены');
                    return;
                case isset($records['message']):
                    throw new \Exception($records['message']);
                    return;
            }

            FinanceSpentFact::makeFromCollab($project, $records['time_records'], $records['related']['Task']);

            $facts = $r->facts->groupBy('finance_res_id');
            $rates = $r->rates;
            $spents = [];
            foreach ($rates as $rate) {
                if (!isset($facts[$rate['id']])) {
                    continue;
                }

                $spentCount = round($facts[$rate['id']]->sum('count'));
                $soldCount = $rate['sold'];
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
            $r->spents()->createMany($spents);*/
        } catch (\Error|\Exception $e) {
            Notification::make()
                ->title($e->getMessage())
                ->danger()
                ->send();
            Log::error($e->getMessage());
            if (config('app.debug')) {
                Log::error($e->getTraceAsString());
            }
        }
    }
}
