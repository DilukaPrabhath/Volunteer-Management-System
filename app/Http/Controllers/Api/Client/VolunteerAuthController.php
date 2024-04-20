<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Volunteer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class VolunteerAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:volunteer-api', ['except' => ['login','register']]);
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Authentication error',
                'errors' => $e->validator->errors()->toArray(),
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = Auth::guard('volunteer-api')->attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::guard('volunteer-api')->user();

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function register(Request $request){
        try {
            $this->validate($request, [
                'first_name'    => 'required|string|min:2|max:255',
                'last_name'     => 'required|string|min:2|max:255',
                'email'         => 'required|string|email|max:255|unique:volunteers,email',
                'mobile'        => 'required|numeric|unique:volunteers,mobile',
                'address'       => 'required|string|min:2|max:255',
                'nic'           => 'required|string|max:255|unique:volunteers,nic',
                'gender'        => 'required',
                'birth_of_date' => 'required',
                'password'      => 'required|string|min:6|max:255',

            ]);
        } catch (ValidationException $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'validation error',
                'errors' => $e->validator->errors()->toArray(),
            ], 422);
        }

        $user = Volunteer::create([
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'email'         => $request->email,
            'mobile'        => $request->mobile,
            'address'       => $request->address,
            'nic'           => $request->nic,
            'gender'        => $request->gender,
            'birth_of_date' => $request->birth_of_date,
            'password'      => Hash::make($request->password),
            'news_letter_sub' => $request->news_letter_sub,
            'user_type'     => "volunteer",
        ]);

        $token = Auth::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'Volunteer created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ], 200);
    }

}
