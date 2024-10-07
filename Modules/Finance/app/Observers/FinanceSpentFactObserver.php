<?php

namespace Modules\Finance\Observers;

use Illuminate\Database\Eloquent\Model;

class FinanceSpentFactObserver
{
    public function saved(Model $r)
    {
        $spents = $r->spents;
        foreach ($spents as $spent) {
            $spent->economy->save();
        }
    }
}
