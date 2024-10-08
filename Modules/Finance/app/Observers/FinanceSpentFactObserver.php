<?php

namespace Modules\Finance\Observers;

use Illuminate\Database\Eloquent\Model;

class FinanceSpentFactObserver
{
    public function saved(Model $r)
    {
        $spents = $r->spents()->where('project_id', $r->project_id)->get();
        foreach ($spents as $spent) {
            $spent->economy->save();
        }
    }
}
