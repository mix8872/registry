<?php

namespace Modules\Finance\Models;

use App\Models\Project;
use App\Traits\HasOwner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinanceEconomy extends Model
{
    use HasFactory;
    use HasOwner;

    public const STATUS_NEW = 'new';
    public const STATUS_PROCESS = 'process';
    public const STATUS_DONE = 'done';
    public const STATUS_ERROR = 'error';

    public static array $statuses = [
        self::STATUS_NEW => 'Новый',
        self::STATUS_PROCESS => 'В процессе',
        self::STATUS_DONE => 'Готов',
        self::STATUS_ERROR => 'Ошибка',
    ];

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

    public function facts(): HasMany
    {
        return $this->hasMany(FinanceSpentFact::class, 'project_id', 'project_id');
    }

    /*public function getStatusAttribute()
    {
        return self::$statuses[$this->attributes['status']];
    }*/

    /**
     * @param string $status
     * @param int|null $jobId
     * @return $this|false
     */
    public function setStatus(string $status, int|null $jobId = null)
    {
        if (!isset(static::$statuses[$status])) {
            return false;
        }
        $this->job_id = $jobId;
        $this->status = $status;
        $this->save();
        return $this;
    }
}
