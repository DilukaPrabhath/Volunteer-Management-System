<?php

namespace App\Http\Controllers\Api\Client\Organization;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignRegister;
use Illuminate\Http\Request;
use App\Traits\Campaign\CampaignTrait;
use App\Http\Requests\Organization\CampaignRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CampaignController extends Controller
{
    use CampaignTrait;

    public function __construct()
    {
        $this->middleware('auth:organization-api');
    }

    public function index(){
        $id = Auth::guard('organization-api')->id();
        $campaigns = Campaign::with('objectives')->where('created_by',$id)->latest('created_at')->paginate(10);

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

    public function store(Request $request){
        try {
            $this->validate($request, [
                'campaign_title' => 'required',
                'location'       => 'required',
                'date'           => 'required',
                'start_time'     => 'required',
                'end_time'       => 'required',
                'overview'       => 'required',
            ]);
        } catch (ValidationException $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'validation error',
                'errors' => $e->validator->errors()->toArray(),
            ], 422);
        }
        return $this->campaignsSaveOrUpdate($request);
    }

    public function update(Request $request){
        try {
            $this->validate($request, [
                'campaign_title' => 'required',
                'location'       => 'required',
                'date'           => 'required',
                'start_time'     => 'required',
                'end_time'       => 'required',
                'overview'       => 'required',
            ]);
        } catch (ValidationException $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'validation error',
                'errors' => $e->validator->errors()->toArray(),
            ], 422);
        }
        return $this->campaignsSaveOrUpdate($request);
    }

    public function view(Request $request){

        $campaignData = Campaign::with('objectives')->where('id',$request->id)->first();

        if (!$campaignData) {
            return response()->json([
                'status' => false,
                'message' => 'No campaigns found.',
                'campaigns' => []
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'campaign data fetched successfully.',
            'campaigns' => $campaignData
        ]);
    }

    public function viewRegisteredVolunteer(Request $request){
        $campaignData = CampaignRegister::with('volunteer')->where('campaign_id', $request->id)->get();
        return $campaignData;
    }


}
