<?php

namespace App\Http\Controllers\Api\Client\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignRegister;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class VolunteerCampaignController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:volunteer-api');
    }

    public function campaignRegister(Request $request){

        try {
            $this->validate($request, [
                'description'              => 'required',
                'week_days_start_time'     => 'required',
                'week_days_end_time'       => 'required',
                'week_end_days_start_time' => 'required',
                'week_end_days_end_time'   => 'required',
            ]);
        } catch (ValidationException $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'validation error',
                'errors' => $e->validator->errors()->toArray(),
            ], 422);
        }
        try {

        $register = [
            'campaign_id'              => $request->campaign_id,
            'registered_user_id'       => Auth::guard('volunteer-api')->id(),
            'description'              => $request->description,
            'week_days_start_time'     => $request->week_days_start_time,
            'week_days_end_time'       => $request->week_days_end_time,
            'week_end_days_start_time' => $request->week_end_days_start_time,
            'week_end_days_end_time'   => $request->week_end_days_end_time,
        ];
            CampaignRegister::updateOrCreate(array('id'=>$request['id']),$register);
            return response()->json(['success' => 'Campaign Registered Successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function registeredCampaignList(){
        $registeredCampaignList = CampaignRegister::with('campaign')->get();
        if (!$registeredCampaignList) {
            return response()->json([
                'status' => false,
                'message' => 'No campaigns found.',
                'campaigns' => []
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Campaigns fetched successfully.',
            'campaigns' => $registeredCampaignList
        ]);
    }



}
