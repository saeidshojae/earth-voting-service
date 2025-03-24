<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Vote;
use App\Models\GroupElection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="رأی‌ها",
 *     description="عملیات مربوط به رأی‌ها"
 * )
 */
class VoteController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/votes/my",
     *     summary="دریافت لیست رأی‌های کاربر جاری",
     *     tags={"رأی‌ها"},
     *     @OA\Response(
     *         response=200,
     *         description="لیست رأی‌های کاربر",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Vote")
     *         )
     *     )
     * )
     */
    public function myVotes()
    {
        $votes = Vote::where('voter_id', Auth::id())
                    ->with('groupElection')
                    ->get();
        
        return response()->json($votes);
    }

    /**
     * @OA\Post(
     *     path="/api/votes",
     *     summary="ثبت رأی جدید",
     *     tags={"رأی‌ها"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"group_election_id", "candidate_id", "position_type"},
     *             @OA\Property(property="group_election_id", type="integer", description="شناسه انتخابات"),
     *             @OA\Property(property="candidate_id", type="integer", description="شناسه کاندیدا"),
     *             @OA\Property(property="position_type", type="string", enum={"board_member", "inspector"}, description="نوع سمت")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="رأی با موفقیت ثبت شد",
     *         @OA\JsonContent(ref="#/components/schemas/Vote")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="خطا در ثبت رأی"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'group_election_id' => 'required|exists:group_elections,id',
            'candidate_id' => 'required|exists:users,id',
            'position_type' => 'required|in:board_member,inspector'
        ]);

        // بررسی وجود رای قبلی
        $existingVote = Vote::where([
            'group_election_id' => $request->group_election_id,
            'voter_id' => Auth::id(),
            'candidate_id' => $request->candidate_id
        ])->first();

        if ($existingVote) {
            return response()->json([
                'message' => 'شما قبلاً به این کاندیدا رای داده‌اید'
            ], 400);
        }

        // بررسی تعداد رای‌های ثبت شده برای هر نوع
        $votesCount = Vote::where([
            'group_election_id' => $request->group_election_id,
            'voter_id' => Auth::id(),
            'position_type' => $request->position_type
        ])->count();

        $maxVotes = $request->position_type === 'board_member' ? 7 : 3;
        if ($votesCount >= $maxVotes) {
            return response()->json([
                'message' => 'شما به حداکثر تعداد مجاز برای این سمت رای داده‌اید'
            ], 400);
        }

        $vote = Vote::create([
            'group_election_id' => $request->group_election_id,
            'voter_id' => Auth::id(),
            'candidate_id' => $request->candidate_id,
            'position_type' => $request->position_type,
            'voted_at' => now()
        ]);

        return response()->json($vote, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/votes/{vote}",
     *     summary="به‌روزرسانی رأی",
     *     tags={"رأی‌ها"},
     *     @OA\Parameter(
     *         name="vote",
     *         in="path",
     *         required=true,
     *         description="شناسه رأی",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"candidate_id"},
     *             @OA\Property(property="candidate_id", type="integer", description="شناسه کاندیدای جدید")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="رأی با موفقیت به‌روزرسانی شد",
     *         @OA\JsonContent(ref="#/components/schemas/Vote")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="عدم دسترسی"
     *     )
     * )
     */
    public function update(Request $request, Vote $vote)
    {
        if ($vote->voter_id !== Auth::id()) {
            return response()->json([
                'message' => 'شما اجازه ویرایش این رای را ندارید'
            ], 403);
        }

        $request->validate([
            'candidate_id' => 'required|exists:users,id'
        ]);

        $vote->update([
            'candidate_id' => $request->candidate_id,
            'voted_at' => now()
        ]);

        return response()->json($vote);
    }

    /**
     * @OA\Delete(
     *     path="/api/votes/{vote}",
     *     summary="حذف رأی",
     *     tags={"رأی‌ها"},
     *     @OA\Parameter(
     *         name="vote",
     *         in="path",
     *         required=true,
     *         description="شناسه رأی",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="رأی با موفقیت حذف شد"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="عدم دسترسی"
     *     )
     * )
     */
    public function destroy(Vote $vote)
    {
        if ($vote->voter_id !== Auth::id()) {
            return response()->json([
                'message' => 'شما اجازه حذف این رای را ندارید'
            ], 403);
        }

        $vote->delete();

        return response()->json([
            'message' => 'رای با موفقیت حذف شد'
        ]);
    }
}
