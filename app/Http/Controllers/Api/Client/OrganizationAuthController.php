<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class OrganizationAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:organization-api', ['except' => ['login','register']]);
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

        if (!$token = Auth::guard('organization-api')->attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        // Retrieve user data after successful authentication
        $user = Auth::guard('organization-api')->user();

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
                'organization_name' => 'required|string|min:2|max:255',
                'address'   => 'required|string|min:2|max:255',
                'contact_no'    => 'required|numeric|unique:organizations,contact_no',
                'email'     => 'required|string|email|max:255|unique:organizations,email',
                'password'  => 'required|string|min:6|max:255',
            ]);
        } catch (ValidationException $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'validation error',
                'errors' => $e->validator->errors()->toArray(),
            ], 422);
        }

        $user = Organization::create([
            'organization_name' => $request->organization_name,
            'address'        => $request->address,
            'email'          => $request->email,
            'contact_no'     => $request->contact_no,
            'password'       => Hash::make($request->password),
            'user_type'     => "organization",
        ]);

        $token = Auth::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'Organization created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ], 200);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}
