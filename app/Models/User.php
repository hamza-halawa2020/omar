<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'profile_image',
        'password',
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
        ];
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

}
