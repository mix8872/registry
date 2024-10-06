<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// use Modules\Finance\Database\Factories\FinanceEconomySpentFactory;

class FinanceEconomySpent extends Model
{
    use HasFactory;

    public $relation = null;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'finance_economy_id',
        'finance_res_id',
        'rate_in',
        'rate_out',
        'sold_count',
        'spent_count',
        'price_in',
        'price_out',
        'performance',
        'profit'
    ];

    public function economy(): BelongsTo
    {
        return $this->belongsTo(FinanceEconomy::class, 'finance_economy_id');
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(FinanceRes::class, 'finance_res_id');
    }

    public function getRelationAttribute()
    {
        return $this->sold_count ? round(($this->spent_count/3600) / $this->sold_count, 2) : 0;
    }
}
