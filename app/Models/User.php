<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Contracts\Permission;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    // User model is stored in Central Database
    protected $connection = 'central';

    protected array $directPermissionIdsByConnection = [];

    protected array $permissionNamesByConnection = [];

    /**
     * Override Spatie roles relation to run on the active default connection (tenant DB when tenancy initialized).
     */
    public function roles(): BelongsToMany
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
    public function permissions(): BelongsToMany
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

        if (! $permission instanceof Permission) {
            return false;
        }

        return in_array((int) $permission->id, $this->directPermissionIds($activeConn), true);
    }

    public function hasPermissionTo($permission, $guardName = null): bool
    {
        $permissionName = $permission instanceof Permission ? $permission->name : (string) $permission;

        return in_array($permissionName, $this->permissionNames(config('database.default')), true);
    }

    private function permissionNames(string $connection): array
    {
        return $this->permissionNamesByConnection[$connection] ??= $this->loadPermissionNames($connection);
    }

    private function loadPermissionNames(string $connection): array
    {
        $tables = config('permission.table_names');
        $columns = config('permission.column_names');
        $modelIdColumn = $columns['model_morph_key'];
        $rolePivotColumn = $columns['role_pivot_key'] ?: 'role_id';
        $permissionPivotColumn = $columns['permission_pivot_key'] ?: 'permission_id';
        $modelType = $this->getMorphClass();
        $guardName = $this->getDefaultGuardName();

        $directPermissions = DB::connection($connection)
            ->table($tables['permissions'])
            ->join($tables['model_has_permissions'], $tables['model_has_permissions'].'.'.$permissionPivotColumn, '=', $tables['permissions'].'.id')
            ->where($tables['model_has_permissions'].'.'.$modelIdColumn, $this->getKey())
            ->where($tables['model_has_permissions'].'.model_type', $modelType)
            ->where($tables['permissions'].'.guard_name', $guardName)
            ->pluck($tables['permissions'].'.name');

        $rolePermissions = DB::connection($connection)
            ->table($tables['permissions'])
            ->join($tables['role_has_permissions'], $tables['role_has_permissions'].'.'.$permissionPivotColumn, '=', $tables['permissions'].'.id')
            ->join($tables['model_has_roles'], $tables['model_has_roles'].'.'.$rolePivotColumn, '=', $tables['role_has_permissions'].'.'.$rolePivotColumn)
            ->where($tables['model_has_roles'].'.'.$modelIdColumn, $this->getKey())
            ->where($tables['model_has_roles'].'.model_type', $modelType)
            ->where($tables['permissions'].'.guard_name', $guardName)
            ->pluck($tables['permissions'].'.name');

        return $directPermissions
            ->merge($rolePermissions)
            ->unique()
            ->values()
            ->all();
    }

    private function directPermissionIds(string $connection): array
    {
        return $this->directPermissionIdsByConnection[$connection] ??= $this->permissions()
            ->pluck(config('permission.table_names.permissions').'.id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'profile_image',
        'password',
        'tenant_id',
        'is_active',
        'whatsapp_api_token',
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
