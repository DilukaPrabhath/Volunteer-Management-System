<?php

namespace App\Http\Controllers\Api\Client\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class VolunteerPublicController extends Controller
{
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
