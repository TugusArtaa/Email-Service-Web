<?php

namespace App\Services;

use App\Models\Application;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;

class ApplicationService
{
    public function getAllApplications(): Collection
    {
        return Application::select('id', 'name', 'description', 'created_at')->get();
    }

    public function createApplication(array $validated): Application
    {
        return Application::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'password' => Hash::make($validated['password']),
            'secret_key' => Application::generateSecretKey(),
        ]);
    }

    public function checkApplicationExists(string $name): bool
    {
        return Application::where('name', $name)->exists();
    }

    public function regenerateSecretKey(Application $application, string $password): ?string
    {
        if (!Hash::check($password, $application->password)) {
            return null;
        }

        $newSecretKey = Application::generateSecretKey();
        $application->update(['secret_key' => $newSecretKey]);

        return $newSecretKey;
    }

    public function formatApplicationResponse(Application $application, ?string $secretKey = null): array
    {
        $response = [
            'application' => [
                'id' => $application->id,
                'name' => $application->name,
                'description' => $application->description,
                'created_at' => $application->created_at,
            ]
        ];

        if ($secretKey) {
            $response['secret_key'] = $secretKey;
        }

        return $response;
    }
}