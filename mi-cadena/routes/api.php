<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\SavingChain\ParticipantController;
use App\Http\Controllers\SavingChain\SavingChainController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/password_reset_link', [AuthController::class, 'password_reset_link']);
Route::post('auth/password_reseted', [AuthController::class, 'password_reseted']);
Route::post('/user', [UserController::class, 'store']);

Route::middleware(['auth:api'])->group(function () {
    Route::post('/user/send_code_verify_user_email', [UserController::class, 'send_code_verify_user_email']);
    Route::post('/user/verify_user_email', [UserController::class, 'verify_user_email']);

    Route::middleware(['verified'])->group(function () {
        Route::get('/user/{id}', [UserController::class, 'show']);
        Route::put('/user/{id}', [UserController::class, 'update']);
        Route::delete('/user/{id}', [UserController::class, 'destroy']);



        Route::resource("/savings_chains", SavingChainController::class);

        Route::post("/participants/invite_users", [ParticipantController::class,'invite_users']);
        Route::post("/participants/decision_invitation", [ParticipantController::class,'decision_invitation']);
        Route::post("/participants/payment_register", [ParticipantController::class,'payment_register']);
        Route::post("/participants/payment_reverse", [ParticipantController::class,'payment_reverse']);
    });
});
