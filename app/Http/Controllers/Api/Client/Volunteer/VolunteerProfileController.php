<?php

namespace App\Http\Controllers\Api\Client\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Volunteer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VolunteerProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:volunteer-api');
    }

    public function profileView(){
        $id = Auth::guard('volunteer-api')->id();
        $profileData = Volunteer::where('id',$id)->first();

        if (!$profileData) {
            return response()->json([
                'status' => false,
                'message' => 'No Profile Data Found.',
                'profileData' => []
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Profile Data Fetched Successfully.',
            'profileData' => $profileData
        ]);
    }
}
