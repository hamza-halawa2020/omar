<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Traits\HasPermissions;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    // User model is stored in Central Database
    protected $connection = 'central';

    /**
     * Override Spatie roles relation to run on the active default connection (tenant DB when tenancy initialized).
     */
    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        $activeConn = config('database.default');

        // Temporarily switch this model's connection so morphToMany builds the query on the tenant DB
        $this->setConnection($activeConn);

        $relation = $this->morphToMany(
            config('permission.models.role'),
            'model',
            config('permission.table_names.model_has_roles'),
            config('permission.column_names.model_morph_key'),
            config('permission.column_names.role_pivot_key') ?: 'role_id'
        );

        // Restore central connection on this model instance
        $this->setConnection('central');

        $relation->getRelated()->setConnection($activeConn);

        return $relation;
    }

    /**
     * Override Spatie permissions relation to run on the active default connection (tenant DB when tenancy initialized).
     */
    public function permissions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        $activeConn = config('database.default');

        // Temporarily switch this model's connection so morphToMany builds the query on the tenant DB
        $this->setConnection($activeConn);

        $relation = $this->morphToMany(
            config('permission.models.permission'),
            'model',
            config('permission.table_names.model_has_permissions'),
            config('permission.column_names.model_morph_key'),
            config('permission.column_names.permission_pivot_key') ?: 'permission_id'
        );

        // Restore central connection on this model instance
        $this->setConnection('central');

        $relation->getRelated()->setConnection($activeConn);

        return $relation;
    }

    /**
     * Override hasDirectPermission to avoid loadMissing() using the central connection.
     * Instead, query the permissions relation directly on the tenant connection.
     */
    public function hasDirectPermission($permission): bool
    {
        $activeConn = config('database.default');
        $permissionClass = $this->getPermissionClass();

        if (is_string($permission)) {
            $permission = $permissionClass->setConnection($activeConn)->findByName($permission, $this->getDefaultGuardName());
        }

        if (is_int($permission)) {
            $permission = $permissionClass->setConnection($activeConn)->findById($permission, $this->getDefaultGuardName());
        }

        if (! $permission instanceof \Spatie\Permission\Contracts\Permission) {
            return false;
        }

        return $this->permissions()->where(
            config('permission.table_names.permissions') . '.id',
            $permission->id
        )->exists();
    }

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'profile_image',
        'password',
        'tenant_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'created_by');
    }

    public function installmentContracts()
    {
        return $this->hasMany(InstallmentContract::class, 'created_by');
    }
    public function installmentPayments()
    {
        return $this->hasMany(InstallmentPayment::class, 'paid_by');
    }
    public function categories()
    {
        return $this->hasMany(Category::class, 'created_by');
    }
    public function paymentWays()
    {
        return $this->hasMany(PaymentWay::class, 'created_by');
    }
    public function paymentWayLogs()
    {
        return $this->hasMany(PaymentWayLog::class, 'created_by');
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'created_by');
    }
    public function transactionLogs()
    {
        return $this->hasMany(TransactionLog::class, 'created_by');
    }

      public function associations()
    {
        return $this->hasMany(AssociationMember::class, 'client_id');
    }
      public function associationPayments()
    {
        return $this->hasMany(AssociationPayment::class, 'client_id');
    }

}
