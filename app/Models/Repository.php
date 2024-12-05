<?php

namespace App\Models;

use App\Traits\HasOwner;
use Chelout\RelationshipEvents\Concerns\HasBelongsToManyEvents;
use Illuminate\Database\Eloquent\Concerns\HasEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Repository extends Model
{
    use HasFactory;
    use HasOwner;
    use HasBelongsToManyEvents;

    public static function boot()
    {
        parent::boot();

        static::belongsToManyAttached(function ($relation, $parent, $ids) {
            if ($relation !== 'servers') {
                return;
            }
            $parent->project->servers()->attach(...$ids);
        });

        static::belongsToManyDetached(function ($relation, $parent, $ids) {
            if ($relation !== 'servers') {
                return;
            }
            $parent->project->servers()->detach(...$ids);
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function servers(): BelongsToMany
    {
        return $this->belongsToMany(Server::class)->withPivot(['url']);
    }

    public function containers(): HasMany
    {
        return $this->hasMany(Container::class);
    }
}
