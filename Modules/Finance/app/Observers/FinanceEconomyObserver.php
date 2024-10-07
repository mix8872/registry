<?php

namespace Modules\Finance\Observers;

use App\Classes\ActiveCollabClient;
use App\Models\Project;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Modules\Finance\Models\FinanceEconomySpent;
use Modules\Finance\Models\FinanceSpentFact;

class FinanceEconomyObserver
{
    public function saved(Model $r)
    {
        $rates = $r->rates;
        $spents = [];
        try {
            $r->spents()->delete();

            if (!$project = Project::find($r->project_id)) {
                throw new \Exception("Проект c id {$r->id} не найден");
            }

            $client = ActiveCollabClient::make();
            $records = $client->get("projects/{$project->crm_id}/time-records")->getJson();

            switch (true) {
                case !$records:
                    throw new \Exception('Записи о затреканном времени не получены');
                    return;
                case isset($records['message']):
                    throw new \Exception($records['message']);
                    return;
            }

            FinanceSpentFact::makeFromCollab($project, $records['time_records'], $records['related']['Task'])
                ->groupBy('finance_res_id');

            $facts = $r->facts->groupBy('finance_res_id');

            foreach ($rates as $rate) {
                if (!isset($facts[$rate['id']])) {
                    continue;
                }

                $spentCount = round($facts[$rate['id']]->sum('count'));
                $soldCount = $rate['sold'];
                $priceIn = $spentCount / 3600 * $rate['in'];
                $priceOut = $soldCount * $rate['out'];

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
            $r->spents()->createMany($spents);
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
