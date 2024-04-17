<?php

namespace App\Http\Controllers\Api\Client\Organization;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;
use App\Traits\Campaign\CampaignTrait;
use App\Http\Requests\Organization\CampaignRequest;
use Illuminate\Validation\ValidationException;

class CampaignController extends Controller
{
    use CampaignTrait;

    public function __construct()
    {
        $this->middleware('auth:organization-api');
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





}
