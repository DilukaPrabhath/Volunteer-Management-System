<?php

namespace App\Traits\Campaign;

use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
                $existingOrganization = Organization::findOrFail($request->id);
                $filename = $existingOrganization->organization_image;
            }
        }

        $campaignData = [
            'campaign_title' => $request['campaign_title'],
            'location'       => $request['location'],
            'date'           => $request['date'],
            'start_time'     => $request['start_time'],
            'end_time'       => $request['end_time'],
            'overview'       => $request['overview'],
            'status'         => 1,
            'created_by'     => ($request['id'] == null) ? Auth::guard('organization-api')->id() : null,
            'updated_by'     => ($request['id'] == null) ? null : Auth::guard('organization-api')->id(),
            'image'          => $filename,
        ];

//        if ($request->hasFile('image')) {
//            $file = $request->file('image');
//            $extension = $file->getClientOriginalExtension();
//            $filename = time() . '.' . $extension;
//            $file->move('image/organization/', $filename);
//            $campaignData['organization_image'] = $filename;
//        }

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
