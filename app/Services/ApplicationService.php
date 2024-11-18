<?php

namespace App\Services;

use App\Models\Application;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ApplicationService
{
    // Method untuk mendapatkan aplikasi dalam database
    public function getAllApplications(): Collection
    {
        return Application::select('id', 'name', 'description', 'created_at')->get();
    }

    // Function untuk membuat dan store data aplikasi di database
    public function getPaginatedApplications(int $perPage = 10, string $orderBy = 'id', string $orderDirection = 'desc', string $search = ''): LengthAwarePaginator
    {
        return Application::select('id', 'name', 'password', 'secret_key', 'created_at')
            ->where('name', 'like', "%$search%")
            ->orderBy($orderBy, $orderDirection)
            ->paginate($perPage);
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

    // Function untuk mengecek apakah aplikasi sudah ada di database
    public function checkApplicationExists(string $name): bool
    {
        return Application::where('name', $name)->exists();
    }

    //Function untuk regenerasi ulang secret key aplikasi
    public function regenerateSecretKey(Application $application, string $password): ?string
    {
        if (!Hash::check($password, $application->password)) {
            return null;
        }

        $newSecretKey = Application::generateSecretKey();
        $application->update(['secret_key' => $newSecretKey]);

        return $newSecretKey;
    }

    //Untuk mengatur format response aplikasi
    public function formatApplicationResponse(Application $application, ?string $secretKey = null): array
    {
        $response = [
            'application' => [
                'id' => $application->id,
                'name' => $application->name,
                'description' => $application->description,
                'created_at' => $application->created_at,
                'secret_key' => $application->secret_key
            ]
        ];

        if ($secretKey) {
            $response['secret_key'] = $secretKey;
        }

        return $response;
    }

    public function deleteApplications(array $ids): void
    {
        Application::whereIn('id', $ids)->delete();
    }
}