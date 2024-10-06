<?php

namespace Modules\Finance\Models;

use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinanceEconomy extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['rates'];

    protected function casts(): array
    {
        return [
            'rates' => 'array',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function spents(): HasMany
    {
        return $this->hasMany(FinanceEconomySpent::class, 'finance_economy_id');
    }

    public function facts(): hasMany
    {
        return $this->hasMany(FinanceSpentFact::class, 'project_id', 'project_id');
    }
}
