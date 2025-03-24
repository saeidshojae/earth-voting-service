<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Vote",
 *     title="رأی",
 *     description="مدل رأی که شامل اطلاعات رأی‌دهنده و کاندیدا است",
 *     @OA\Property(property="id", type="integer", format="int64", description="شناسه یکتای رأی"),
 *     @OA\Property(property="group_election_id", type="integer", format="int64", description="شناسه انتخابات"),
 *     @OA\Property(property="voter_id", type="integer", format="int64", description="شناسه رأی‌دهنده"),
 *     @OA\Property(property="candidate_id", type="integer", format="int64", description="شناسه کاندیدا"),
 *     @OA\Property(property="position_type", type="string", enum={"board_member", "inspector"}, description="نوع سمت"),
 *     @OA\Property(property="is_delegated", type="boolean", description="آیا این رأی از طریق تفویض داده شده است"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_election_id',
        'voter_id',
        'candidate_id',
        'position_type',
        'is_delegated'
    ];

    protected $casts = [
        'is_delegated' => 'boolean'
    ];

    public function groupElection()
    {
        return $this->belongsTo(GroupElection::class);
    }

    public function voter()
    {
        return $this->belongsTo(User::class, 'voter_id');
    }

    public function candidate()
    {
        return $this->belongsTo(User::class, 'candidate_id');
    }
}
