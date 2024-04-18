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

    public function index(){
        $campaigns = Campaign::with('objectives')->latest('created_at')->paginate(10);

        if (!$campaigns) {
            return response()->json([
                'status' => false,
                'message' => 'No campaigns found.',
                'campaigns' => []
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Campaigns fetched successfully.',
            'campaigns' => $campaigns
        ]);
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

    public function campaignSearch(Request $request){
        try {
            $this->validate($request, [
                'search'              => 'required',
            ]);
        } catch (ValidationException $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'validation error',
                'errors' => $e->validator->errors()->toArray(),
            ], 422);
        }

        $query = Campaign::with('objectives')->where('overview', 'like', '%' . $request->search. '%');
        $searchResults = $query->get();

        if (!$searchResults) {
            return response()->json([
                'status' => false,
                'message' => 'No campaigns found.',
                'campaigns' => []
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Campaigns fetched successfully.',
            'campaigns' => $searchResults,
        ]);
    }

    public function campaignFilter(Request $request){

        $query = Campaign::with('objectives');

        if ($request->filled('categories')) {
            $query->where('categories', $request->categories);
        }

        if ($request->filled('skill')) {
            $query->where('skill', $request->skill);
        }

        if ($request->filled('duration')) {
            $query->where('duration', $request->duration);
        }

        $filterResults = $query->get();

        if (!$filterResults) {
            return response()->json([
                'status' => false,
                'message' => 'No campaigns found.',
                'campaigns' => []
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Campaigns fetched successfully.',
            'campaigns' => $filterResults,
        ]);

    }

}
