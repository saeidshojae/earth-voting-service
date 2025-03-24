<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\VoteDelegation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="تفویض رأی",
 *     description="عملیات مربوط به تفویض رأی"
 * )
 */
class VoteDelegationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/delegations/my",
     *     summary="دریافت لیست تفویض‌های رأی کاربر جاری",
     *     tags={"تفویض رأی"},
     *     @OA\Response(
     *         response=200,
     *         description="لیست تفویض‌های رأی کاربر",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/VoteDelegation")
     *         )
     *     )
     * )
     */
    public function myDelegations()
    {
        $delegations = VoteDelegation::where('delegator_id', Auth::id())
                                   ->with('delegate')
                                   ->get();
        
        return response()->json($delegations);
    }

    /**
     * @OA\Get(
     *     path="/api/delegations/received",
     *     summary="دریافت لیست تفویض‌های دریافتی کاربر جاری",
     *     tags={"تفویض رأی"},
     *     @OA\Response(
     *         response=200,
     *         description="لیست تفویض‌های دریافتی کاربر",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/VoteDelegation")
     *         )
     *     )
     * )
     */
    public function receivedDelegations()
    {
        $delegations = VoteDelegation::where('delegate_id', Auth::id())
                                   ->where('is_active', true)
                                   ->with('delegator')
                                   ->get();
        
        return response()->json($delegations);
    }

    /**
     * @OA\Post(
     *     path="/api/delegations",
     *     summary="ایجاد تفویض رأی جدید",
     *     tags={"تفویض رأی"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"delegate_id", "expertise_area", "start_date"},
     *             @OA\Property(property="delegate_id", type="integer", description="شناسه نماینده"),
     *             @OA\Property(property="expertise_area", type="string", description="حوزه تخصصی"),
     *             @OA\Property(property="start_date", type="string", format="date", description="تاریخ شروع"),
     *             @OA\Property(property="end_date", type="string", format="date", description="تاریخ پایان")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تفویض رأی با موفقیت ایجاد شد",
     *         @OA\JsonContent(ref="#/components/schemas/VoteDelegation")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="خطا در ایجاد تفویض رأی"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'delegate_id' => 'required|exists:users,id',
            'expertise_area' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date'
        ]);

        // بررسی تفویض قبلی در همان حوزه تخصصی
        $existingDelegation = VoteDelegation::where([
            'delegator_id' => Auth::id(),
            'expertise_area' => $request->expertise_area,
            'is_active' => true
        ])->first();

        if ($existingDelegation) {
            return response()->json([
                'message' => 'شما قبلاً در این حوزه تخصصی تفویض رای داده‌اید'
            ], 400);
        }

        $delegation = VoteDelegation::create([
            'delegator_id' => Auth::id(),
            'delegate_id' => $request->delegate_id,
            'expertise_area' => $request->expertise_area,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => true
        ]);

        return response()->json($delegation, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/delegations/{delegation}",
     *     summary="به‌روزرسانی تفویض رأی",
     *     tags={"تفویض رأی"},
     *     @OA\Parameter(
     *         name="delegation",
     *         in="path",
     *         required=true,
     *         description="شناسه تفویض رأی",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="end_date", type="string", format="date", description="تاریخ پایان جدید"),
     *             @OA\Property(property="is_active", type="boolean", description="وضعیت فعال بودن")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تفویض رأی با موفقیت به‌روزرسانی شد",
     *         @OA\JsonContent(ref="#/components/schemas/VoteDelegation")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="عدم دسترسی"
     *     )
     * )
     */
    public function update(Request $request, VoteDelegation $delegation)
    {
        if ($delegation->delegator_id !== Auth::id()) {
            return response()->json([
                'message' => 'شما اجازه ویرایش این تفویض را ندارید'
            ], 403);
        }

        $request->validate([
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean'
        ]);

        $delegation->update([
            'end_date' => $request->end_date,
            'is_active' => $request->is_active ?? $delegation->is_active
        ]);

        return response()->json($delegation);
    }

    /**
     * @OA\Delete(
     *     path="/api/delegations/{delegation}",
     *     summary="غیرفعال کردن تفویض رأی",
     *     tags={"تفویض رأی"},
     *     @OA\Parameter(
     *         name="delegation",
     *         in="path",
     *         required=true,
     *         description="شناسه تفویض رأی",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تفویض رأی با موفقیت غیرفعال شد"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="عدم دسترسی"
     *     )
     * )
     */
    public function destroy(VoteDelegation $delegation)
    {
        if ($delegation->delegator_id !== Auth::id()) {
            return response()->json([
                'message' => 'شما اجازه حذف این تفویض را ندارید'
            ], 403);
        }

        $delegation->update(['is_active' => false]);

        return response()->json([
            'message' => 'تفویض رای با موفقیت غیرفعال شد'
        ]);
    }
}
