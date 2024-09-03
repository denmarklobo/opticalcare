<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    // Simple in-memory storage for tokens (use a database in production)
    protected $tokens = [];

    // Store a new admin
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:admins',
                'password' => 'required|min:6',
            ]);

            $hashedPassword = Hash::make($request->password);

            $admin = Admin::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $hashedPassword,
                'role' => 'admin',
            ]);

            return response()->json($admin, 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Handle login and generate token
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            // Generate a new token (in a real application, use a secure storage solution)
            $token = Str::random(60);
            $this->tokens[$admin->id] = $token;

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // Authenticate the admin using the token
    public function authenticate(Request $request)
    {
        $request->validate([
            'token' => 'required',
        ]);

        $adminId = array_search($request->token, $this->tokens);

        if ($adminId !== false) {
            $admin = Admin::find($adminId);
            if ($admin) {
                return response()->json(['message' => 'Authenticated', 'admin' => $admin]);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
