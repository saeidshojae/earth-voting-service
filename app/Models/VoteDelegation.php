<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="VoteDelegation",
 *     title="تفویض رأی",
 *     description="مدل تفویض رأی که شامل اطلاعات تفویض‌دهنده و تفویض‌گیرنده است",
 *     @OA\Property(property="id", type="integer", format="int64", description="شناسه یکتای تفویض"),
 *     @OA\Property(property="delegator_id", type="integer", format="int64", description="شناسه تفویض‌دهنده"),
 *     @OA\Property(property="delegate_id", type="integer", format="int64", description="شناسه تفویض‌گیرنده"),
 *     @OA\Property(property="expertise_area", type="string", description="حوزه تخصصی تفویض"),
 *     @OA\Property(property="is_active", type="boolean", description="وضعیت فعال بودن تفویض"),
 *     @OA\Property(property="expiry_date", type="string", format="date-time", nullable=true, description="تاریخ انقضای تفویض"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class VoteDelegation extends Model
{
    use HasFactory;

    protected $fillable = [
        'delegator_id',
        'delegate_id',
        'expertise_area',
        'is_active',
        'expiry_date'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expiry_date' => 'datetime'
    ];

    public function delegator()
    {
        return $this->belongsTo(User::class, 'delegator_id');
    }

    public function delegate()
    {
        return $this->belongsTo(User::class, 'delegate_id');
    }
}
