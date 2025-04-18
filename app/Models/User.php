<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'is_active_member'
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
        'is_active_member' => 'boolean'
    ];

    // رأی‌های داده شده توسط کاربر
    public function votes()
    {
        return $this->hasMany(Vote::class, 'voter_id');
    }

    // رأی‌هایی که به کاربر داده شده (به عنوان کاندیدا)
    public function receivedVotes()
    {
        return $this->hasMany(Vote::class, 'candidate_id');
    }

    // تفویض‌های رأی که کاربر انجام داده
    public function givenDelegations()
    {
        return $this->hasMany(VoteDelegation::class, 'delegator_id');
    }

    // تفویض‌های رأی که به کاربر داده شده
    public function receivedDelegations()
    {
        return $this->hasMany(VoteDelegation::class, 'delegate_id');
    }
}
