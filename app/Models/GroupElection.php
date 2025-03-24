<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="GroupElection",
 *     title="انتخابات گروهی",
 *     description="مدل انتخابات گروهی که شامل اطلاعات اصلی انتخابات است",
 *     @OA\Property(property="id", type="integer", format="int64", description="شناسه یکتای انتخابات"),
 *     @OA\Property(property="title", type="string", description="عنوان انتخابات"),
 *     @OA\Property(property="description", type="string", nullable=true, description="توضیحات انتخابات"),
 *     @OA\Property(property="start_date", type="string", format="date-time", description="تاریخ شروع انتخابات"),
 *     @OA\Property(property="end_date", type="string", format="date-time", description="تاریخ پایان انتخابات"),
 *     @OA\Property(property="status", type="string", enum={"draft", "active", "completed", "cancelled"}, description="وضعیت انتخابات"),
 *     @OA\Property(property="board_member_count", type="integer", description="تعداد اعضای هیئت مدیره"),
 *     @OA\Property(property="inspector_count", type="integer", description="تعداد بازرسان"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class GroupElection extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'status',
        'board_member_count',
        'inspector_count'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function votingSettings()
    {
        return $this->hasOne(VotingSetting::class);
    }
}
