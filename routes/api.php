<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\GroupElectionController;
use App\Http\Controllers\API\VoteController;
use App\Http\Controllers\API\VoteDelegationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    // مسیرهای مربوط به انتخابات
    Route::get('/elections', [GroupElectionController::class, 'index']);
    Route::get('/elections/{groupElection}', [GroupElectionController::class, 'show']);
    Route::post('/elections/start', [GroupElectionController::class, 'start'])->middleware('admin');
    Route::post('/elections/{groupElection}/process', [GroupElectionController::class, 'process'])->middleware('admin');
    Route::get('/elections/{groupElection}/results', [GroupElectionController::class, 'results']);

    // مسیرهای مربوط به رأی‌ها
    Route::get('/votes/my', [VoteController::class, 'myVotes']);
    Route::post('/votes', [VoteController::class, 'store'])->middleware('active.member');
    Route::put('/votes/{vote}', [VoteController::class, 'update']);
    Route::delete('/votes/{vote}', [VoteController::class, 'destroy']);

    // مسیرهای مربوط به تفویض رأی
    Route::get('/delegations/my', [VoteDelegationController::class, 'myDelegations']);
    Route::get('/delegations/received', [VoteDelegationController::class, 'receivedDelegations']);
    Route::post('/delegations', [VoteDelegationController::class, 'store'])->middleware('active.member');
    Route::put('/delegations/{delegation}', [VoteDelegationController::class, 'update']);
    Route::delete('/delegations/{delegation}', [VoteDelegationController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
