<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;

class HasOwnerObserver
{
    /**
     * Handle the Model "creating" event.
     *
     * @param Model $r
     * @return void
     */
    public function creating(Model $r): void
    {
        $r->created_by = $r->created_by ?: auth()->id();
        $r->updated_by = $r->updated_by ?: auth()->id();
    }

    public function updating(Model $r)
    {
        $r->updated_by = $r->updated_by ?: auth()->id();
    }
}
