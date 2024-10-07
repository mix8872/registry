<?php

namespace Modules\Finance\Models;

use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

// use Modules\Finance\Database\Factories\FinanceSpentFactFactory;

class FinanceSpentFact extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'project_id',
        'crm_id',
        'date',
        'count',
        'finance_res_id',
        'task_url',
        'comment',
    ];

    public function resource(): BelongsTo
    {
        return $this->belongsTo(FinanceRes::class, 'finance_res_id', 'id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function spents(): HasMany
    {
        return $this->hasMany(FinanceEconomySpent::class, 'finance_res_id', 'finance_res_id');
    }

    /* protected function getCountAttribute(): string
     {
         return date('H:i', $this->attributes['count']);
     }*/

    protected function setCountAttribute($value)
    {
        if (is_string($value)) {
            sscanf($value, "%d:%d", $hours, $minutes);
            $this->attributes['count'] = $hours * 3600 + $minutes * 60;
        } else {
            $this->attributes['count'] = $value;
        }
    }

    /**
     * Создание проекта на основе данных из ActiveCollab
     *
     * @param Project $project
     * @param array $records
     * @param array $tasks
     * @return Collection
     */
    public static function makeFromCollab(Project $project, array $records, array $tasks): Collection
    {
        $host = config('services.collab.host');
        $resources = FinanceRes::get()->keyBy('crm_id');
        $arr = [];
        foreach ($records as $record) {
            try {
                $task = $tasks[$record['parent_id']] ?? null;
                $attributes = [
                    'project_id' => $project->id,
                    'crm_id' => $record['id'],
                    'date' => date("Y-m-d H:i:s", $record['record_date']),
                    'count' => (int)round($record['value'] * 3600),
                    'finance_res_id' => $resources[$record['job_type_id']]->id,
                    'task_url' => $task ? "{$host}{$task['url_path']}" : '',
                    'comment' => $record['summary'],
                ];
                $spentFact = FinanceSpentFact::firstOrNew(['crm_id' => $record['id']], $attributes);
                if ($spentFact->exists) {
                    $spentFact->update($attributes);
                }
                $spentFact->save();
                $arr[] = $spentFact;
            } catch (\Error|\Exception $e) {
                continue;
            }
        }
        return collect($arr);
    }
}
