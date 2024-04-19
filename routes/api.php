<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Client\OrganizationAuthController;
use App\Http\Controllers\Api\Client\VolunteerAuthController;
use App\Http\Controllers\Api\Client\Organization\CampaignController;
use App\Http\Controllers\Api\Client\Organization\ProfileController;
use App\Http\Controllers\Api\Client\Volunteer\VolunteerCampaignController;
use App\Http\Controllers\Api\Client\Volunteer\VolunteerProfileController;
use App\Http\Controllers\Api\Client\Volunteer\VolunteerPublicController;

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

// Public accessible API
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/refresh', [AuthController::class, 'refresh']);

Route::post('/organization/register', [OrganizationAuthController::class, 'register']);
Route::post('/organization/login', [OrganizationAuthController::class, 'login']);

Route::post('/volunteer/register', [VolunteerAuthController::class, 'register']);
Route::post('/volunteer/login', [VolunteerAuthController::class, 'login']);

Route::get('/volunteer/campaign', [VolunteerPublicController::class, 'index']);
Route::post('/volunteer/campaign/search', [VolunteerPublicController::class, 'campaignSearch']);
Route::post('/volunteer/campaign/filter', [VolunteerPublicController::class, 'campaignFilter']);

Route::middleware('volunteer-api')->group(function () {
    Route::post('/volunteer/campaign/register', [VolunteerCampaignController::class, 'campaignRegister']);
    Route::get('/volunteer/campaign/register-list', [VolunteerCampaignController::class, 'registeredCampaignList']);
    Route::get('/volunteer/profile-view', [VolunteerProfileController::class, 'profileView']);
});

Route::middleware('organization-api')->group(function () {
    Route::get('/organization/campaign', [CampaignController::class, 'index']);
    Route::post('/organization/campaign/store', [CampaignController::class, 'store']);
    Route::post('/organization/campaign/update', [CampaignController::class, 'update']);
    Route::get('/organization/profile', [ProfileController::class, 'profileView']);
    Route::post('/organization/profile/update', [ProfileController::class, 'profileUpdate']);
});
