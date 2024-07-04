<?php

namespace App\Models;

use App\Traits\HasOwner;
use Illuminate\Database\Eloquent\Concerns\HasEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class Project extends Model
{
    use HasFactory;
    use HasEvents;
    use HasOwner;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_ARCHIVED = 'archived';

    public static array $statuses = [
        self::STATUS_ACTIVE => 'Активен',
        self::STATUS_ARCHIVED => 'В архиве'
    ];

    public $fillable = [
        'name',
        'comment',
        'status',
        'crm_id',
        'crm_url',
        'created_by',
        'updated_by',
        'created_at'
    ];

    public function stacks(): BelongsToMany
    {
        return $this->belongsToMany(Stack::class);
    }

    public function repositories(): HasMany
    {
        return $this->hasMany(Repository::class);
    }

    public function servers(): BelongsToMany
    {
        return $this->belongsToMany(Server::class);
    }

    /**
     * @param integer|null $updated_by User id
     * @return void
     */
    public function archive(int|null $updated_by = null): void
    {
        $this->status = self::STATUS_ARCHIVED;
        if ($updated_by) {
            $this->updated_by = $updated_by;
        }
        $this->save();
    }

    /**
     * @param integer|null $updated_by User id
     * @return void
     */
    public function unarchive(int|null $updated_by = null): void
    {
        $this->status = self::STATUS_ACTIVE;
        if ($updated_by) {
            $this->updated_by = $updated_by;
        }
        $this->save();
    }
}
