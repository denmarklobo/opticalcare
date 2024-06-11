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
                'birthdate' => 'required|date',
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

    public function update(Request $request, Patient $patient)
    {
        try {
            $request->validate([
                'email' => 'required|email|unique:patients,email,' . $patient->id,
                'password' => 'required|min:6',
            ]);

            $patient->update($request->all());

            return response()->json($patient, 200);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Patient $patient)
    {
        try {
            $patient->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
