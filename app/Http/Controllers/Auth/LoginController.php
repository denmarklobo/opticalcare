<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function authenticate(Request $request)
    {

       $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation Error',
            'errors' => $validator->errors(),
        ], 422);
    }

    if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
   
        $token = auth()->user()->createToken('auth_token')->plainTextToken;

        // Return success response with token
        return response()->json([
            'message' => 'Authenticated',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    // Authentication failed, return error response
    return response()->json([
        'message' => 'Unauthenticated',
    ], 401);
    }
}
