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
            if (in_array($r->status, [FinanceEconomy::STATUS_NEW, FinanceEconomy::STATUS_RECALC])) {
                ImportSpentFacts::dispatch($r->project_id, $r->id);
            }
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
