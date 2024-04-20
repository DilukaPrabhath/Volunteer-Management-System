<?php

namespace App\Traits\Campaign;

use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

trait CampaignTrait
{

    public function campaignsSaveOrUpdate($request)
    {
        try {
            DB::transaction(function () use($request) {
                $model = $this->saveOrupdateCampaignsData($request);
                $this->syncCampaignObjectiveType($model, $request['objective_title']);
            });
            return $this->successResponse();

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    protected function saveOrUpdateCampaignsData($request)
    {
        if($request->hasfile('image')){
            $file =$request->file('image');
            $extension=$file->getClientOriginalExtension();
            $filename=time().'.'.$extension;
            $file->move('image/campaign/',$filename);
        }else{
            $filename = "";
            if ($request->has('id')) {
                $existingCampaign = Campaign::findOrFail($request->id);
                $filename = $existingCampaign->image;
            }
        }

        $campaignData = [
            'campaign_title' => $request['campaign_title'],
            'location'       => $request['location'],
            'date'           => $request['date'],
            'start_time'     => $request['start_time'],
            'end_time'       => $request['end_time'],
            'overview'       => $request['overview'],
            'categories'     => $request['categories'],
            'skill'          => $request['skill'],
            'duration'       => $request['duration'],
            'status'         => 1,
            'updated_by'     => Auth::guard('organization-api')->id(),
            'image'          => $filename,
        ];
        $existingCampaign = null;
        if ($request->filled('id')) {
            $existingCampaign = Campaign::findOrFail($request->id);
        }

        if (!$request->filled('id')) {
            $campaignData['created_by'] = Auth::guard('organization-api')->id();
        } else {
            $campaignData['created_by'] = $existingCampaign->created_by;
        }

        $campaign = Campaign::updateOrCreate(array('id'=>$request['id']),$campaignData);
        return $campaign;
    }

    protected function syncCampaignObjectiveType($model, $campObjectIds)
    {

        $model->campaignObjective()
            ->sync([]);

        $model->campaignObjective()
            ->sync($campObjectIds);
    }

    protected function successResponse()
    {
        return response()->json(['success' => 'Campaign Created Successfully']);
    }

    protected function errorResponse($message)
    {
        return response()->json(['error' => $message], 500);
    }
}
