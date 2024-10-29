<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = Application::select('id', 'name', 'description', 'created_at')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => $applications->isEmpty() ? 'No applications found' : 'Applications retrieved successfully',
            'data' => [
                'applications' => $applications
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:applications'],
            'description' => ['required', 'string'],
            'password' => ['required', Password::min(8)],
        ]);

        $application = Application::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'password' => Hash::make($validated['password']),
            'secret_key' => Application::generateSecretKey(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Application registered successfully',
            'data' => [
                'application' => [
                    'id' => $application->id,
                    'name' => $application->name,
                    'description' => $application->description,
                    'created_at' => $application->created_at,
                ],
                'secret_key' => $application->secret_key
            ]
        ], 201);
    }

    public function show(Application $application)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Application details retrieved successfully',
            'data' => [
                'application' => [
                    'id' => $application->id,
                    'name' => $application->name,
                    'description' => $application->description,
                    'created_at' => $application->created_at,
                ]
            ]
        ]);
    }

    public function regenerateSecret(Request $request, Application $application)
    {
        $request->validate([
            'password' => ['required'],
        ]);

        if (!Hash::check($request->password, $application->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid password',
            ], 401);
        }

        $newSecretKey = Application::generateSecretKey();
        $application->update([
            'secret_key' => $newSecretKey,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Secret key regenerated successfully',
            'data' => [
                'secret_key' => $newSecretKey
            ]
        ]);
    }
}