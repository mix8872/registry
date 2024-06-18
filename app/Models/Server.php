<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Server extends Model
{
    use HasFactory;

    public function repositories(): BelongsToMany
    {
        return $this->belongsToMany(Repository::class)->withPivot(['type', 'url']);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(IpAddress::class);
    }

    public function containers(): HasMany
    {
        return $this->hasMany(Container::class);
    }
}
