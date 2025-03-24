<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="VotingSetting",
 *     title="تنظیمات رأی‌گیری",
 *     description="مدل تنظیمات رأی‌گیری که شامل تنظیمات مربوط به یک انتخابات است",
 *     @OA\Property(property="id", type="integer", format="int64", description="شناسه یکتای تنظیمات"),
 *     @OA\Property(property="group_election_id", type="integer", format="int64", description="شناسه انتخابات"),
 *     @OA\Property(property="allow_delegation", type="boolean", description="آیا تفویض رأی مجاز است"),
 *     @OA\Property(property="delegation_deadline", type="string", format="date-time", nullable=true, description="مهلت تفویض رأی"),
 *     @OA\Property(property="minimum_vote_count", type="integer", description="حداقل تعداد رأی مجاز"),
 *     @OA\Property(property="maximum_vote_count", type="integer", description="حداکثر تعداد رأی مجاز"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class VotingSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_election_id',
        'allow_delegation',
        'delegation_deadline',
        'minimum_vote_count',
        'maximum_vote_count'
    ];

    protected $casts = [
        'allow_delegation' => 'boolean',
        'delegation_deadline' => 'datetime'
    ];

    public function groupElection()
    {
        return $this->belongsTo(GroupElection::class);
    }
}
