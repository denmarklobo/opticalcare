<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::all();
        return response()->json($patients);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|unique:patients',
                'full_name' => 'required',
                'password' => 'required|min:6',
                'address' => 'required',
                'contact' => 'required',
                // 'birthdate' => 'required|date',
            ]); 

            $hashedPassword = Hash::make($request->password);

            $user = User::create([
                'name' => $request->full_name,
                'email' => $request->email,
                'password' => $hashedPassword,
            ]);

            $patient = Patient::create([
                'user_id' => $user->id,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'address' => $request->address,
                'contact' => $request->contact,
                'birthdate' => $request->birthdate,
                'password' => $hashedPassword, 
            ]);

            return response()->json($patient, 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
       try {
            $patient = Patient::findOrFail($id);
            return response()->json($patient);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Patient not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'address' => 'required|string',
            'contact' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'nullable|string',
            'newPassword' => 'nullable|string|min:6',
        ]);

        $patient = Patient::findOrFail($id);
        $user = $patient->user; // Assuming there is a relation between Patient and User

        if ($request->has('password') && !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['password' => ['The provided password is incorrect.']]);
        }

        $patient->address = $request->address;
        $patient->contact = $request->contact;
        $patient->email = $request->email;

        if ($request->filled('newPassword')) {
            $user->password = Hash::make($request->newPassword);
        }

        $user->email = $request->email;
        
        $patient->save();
        $user->save();

        return response()->json(['message' => 'Patient information updated successfully'], 200);
    }

    public function destroy(Patient $patient)
    {
        try {
            // Check if the patient has an associated user
            if ($patient->user) {
                $patient->user->delete(); // Delete the user record
            }
            
            // Delete the patient record
            $patient->delete();

            return response()->json(null, 204); // Return no content response for successful deletion
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500); // Return error response
        }
    }
}
