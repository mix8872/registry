<?php

namespace App\Models;

use App\Traits\HasOwner;
use Illuminate\Database\Eloquent\Concerns\HasEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;

class Project extends Model
{
    use HasFactory;
    use HasEvents;
    use HasOwner;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_SUPPORT = 'support';
    public const STATUS_ARCHIVED = 'archived';

    public const PAYMENT_TM = 'tm';
    public const PAYMENT_FIX = 'fix';

    public const PAYMENT_PERIOD_MONTH = 'month';
    public const PAYMENT_PERIOD_STEP = 'step';
    public const PAYMENT_PERIOD_ONCE = 'once';

    public const LEGAL_LTD = 'ltd';
    public const LEGAL_IE = 'ie';

    public static array $statuses = [
        self::STATUS_ACTIVE => 'Активен',
        self::STATUS_SUPPORT => 'Поддержка',
        self::STATUS_ARCHIVED => 'Закрыт'
    ];

    public static array $payments = [
        self::PAYMENT_FIX => 'Fix',
        self::PAYMENT_TM => 'T&M',
    ];

    public static array $paymentPeriods = [
        self::PAYMENT_PERIOD_STEP => 'Поэтапно',
        self::PAYMENT_PERIOD_ONCE => 'Разово',
        self::PAYMENT_PERIOD_MONTH => 'Ежемесячно',
    ];

    public static array $legals = [
        self::LEGAL_IE => 'ИП',
        self::LEGAL_LTD => 'ООО'
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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function workType(): BelongsTo
    {
        return $this->belongsTo(WorkType::class);
    }

    public function getPaymentAttribute()
    {
        return $this->payment_type ? self::$payments[$this->payment_type] : null;
    }

    public function getPeriodAttribute()
    {
        return $this->payment_period ? self::$paymentPeriods[$this->payment_period] : null;
    }

    public function getLegalAttribute()
    {
        return $this->legal_inner ? self::$legals[$this->legal_inner] : null;
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
