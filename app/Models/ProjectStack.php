<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProjectStack extends Pivot
{
    public $timestamps = false;

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function stack(): BelongsTo
    {
        return $this->belongsTo(Stack::class);
    }
}
