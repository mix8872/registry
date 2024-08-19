<?php

namespace App\Models;

use App\Traits\HasOwner;
use Illuminate\Database\Eloquent\Concerns\HasEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
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

    /**
     * Создание проекта на основе данных из ActiveCollab
     *
     * @param array $payload Массив с данными о проекте из ActiveCollab
     * @return Project
     */
    public static function makeFromCollab(array $payload): Project
    {
        $host = config('services.collab.host');
        $user = User::firstOrCreate(['email' => $payload['created_by_email']], [
            'name' => substr($payload['created_by_email'], 0, strpos($payload['created_by_email'], '@')),
            'email' => $payload['created_by_email'],
            'password' => Hash::make($payload['created_by_email'])
        ]);
        $attributes = [
            'name' => $payload['name'],
            'comment' => $payload['body'],
            'status' => $payload['is_trashed'] ? Project::STATUS_ARCHIVED : Project::STATUS_ACTIVE,
            'crm_id' => $payload['id'],
            'crm_url' => "{$host}{$payload['url_path']}",
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'created_at' => date('Y-m-d H:i:s', $payload['created_on']),
        ];
        $projectModel = Project::firstOrNew(['crm_id' => $payload['id']], $attributes);
        if ($projectModel->exists) {
            $projectModel->update($attributes);
        }
        $projectModel->save();

        return $projectModel;
    }
}
