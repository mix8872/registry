<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FinanceRes extends Model
{
    use HasFactory;

    public const TYPE_INTERNAL = 'internal';
    public const TYPE_OTHER = 'other';
    public const TYPE_LICENSE = 'license';

    public static $types = [
        self::TYPE_INTERNAL => 'Внутренний',
        self::TYPE_OTHER => 'Внешний',
        self::TYPE_LICENSE => 'Лицензия'
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['crm_id', 'name', 'cost_in', 'cost_out'];

    public function spentFacts(): BelongsToMany
    {
        return $this->belongsToMany(FinanceSpentFact::class, 'finance_res_id');
    }

    public static function makeFromCollab($type)
    {
        $attributes = [
            'name' => $type['name'],
            'cost_in' => $type['default_hourly_rate'],
            'cost_out' => $type['default_hourly_rate'],
        ];
        $resource = self::firstOrNew(['crm_id' => $type['id']], $attributes);
        if ($resource->exists) {
            $resource->update($attributes);
        }
        $resource->save();
        return $resource;
    }
}
