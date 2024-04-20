<?php

namespace App\Http\Controllers\Api\Client\Organization;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function profileView(){
        $id = Auth::guard('organization-api')->id();
        $profileData = Organization::where('id',$id)->first();

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

    public function profileUpdate(Request $request){
        try {
            $this->validate($request, [
                'organization_name' => 'required|string|min:2|max:255',
                'address'       => 'required|string|min:2|max:255',
                'contact_no'    => ['required','numeric',
                    Rule::unique('organizations', 'contact_no')->ignore($request->id),],
                'email' => ['required','string','email','max:255',
                    Rule::unique('organizations', 'email')->ignore($request->id),],
            ]);
        } catch (ValidationException $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'validation error',
                'errors' => $e->validator->errors()->toArray(),
            ], 422);
        }



        try {
            DB::transaction(function () use($request) {

                if($request->hasfile('organization_image')){
                    $file =$request->file('organization_image');
                    $extension=$file->getClientOriginalExtension();
                    $filename=time().'.'.$extension;
                    $file->move('image/organization/',$filename);
                }else{
                    $filename = "";
                    if ($request->has('id')) {
                        $existingOrganization = Organization::findOrFail($request->id);
                        $filename = $existingOrganization->organization_image;
                    }
                }

                $organization = [
                    'organization_name' => $request->organization_name,
                    'address'           => $request->address,
                    'email'             => $request->email,
                    'contact_no'        => $request->contact_no,
                    'organization_image' => $filename,
                ];

                if (!$request->password == null) {
                    $organization['password'] = Hash::make($request->password);
                }

                Organization::updateOrCreate(array('id'=>$request['id']),$organization);
            });
            return response()->json(['success' => 'Campaign Created Successfully']);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }





    }
}
