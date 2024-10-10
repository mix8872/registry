<?php

namespace Modules\Finance\Observers;

use Illuminate\Database\Eloquent\Model;

class FinanceSpentFactObserver
{
    public function saved(Model $r)
    {
        $r->economy->save();
    }

    public function deleted(Model $r)
    {
        $r->economy->save();
    }
}
