<?php

namespace App\Models;

use App\Traits\HasOwner;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Server extends Model
{
    use HasFactory;
    use HasOwner;

    public static array $checklistOptions = [
        'ssh_protected' => 'Защита SSH',
        'has_admin_user' => 'Создан пользователь admin',
        'set_admin_password' => 'Создан пароль пользователю admin',
        'set_root_password' => 'Изменён пароль по-умолчанию для root',
        'has_backup' => 'Настроено резервное копирование'
    ];

    protected function casts(): array
    {
        return [
            'checklist' => 'array',
        ];
    }

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

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }
}
