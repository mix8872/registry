<?php

namespace App\Models;

use App\Traits\HasOwner;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Repository extends Model
{
    use HasFactory;
    use HasOwner;

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function servers(): BelongsToMany
    {
        return $this->belongsToMany(Server::class)->withPivot(['type', 'url']);
    }

    public function containers(): HasMany
    {
        return $this->hasMany(Container::class);
    }
}
