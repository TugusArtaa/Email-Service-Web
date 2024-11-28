<?php

namespace App\Services;

use App\Models\Application;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
        return Application::select('id', 'name', 'pic_name', 'secret_key', 'created_at')
            ->where('name', 'like', "%$search%")
            ->orderBy($orderBy, $orderDirection)
            ->paginate($perPage);
    }

    public function createApplication(array $validated): Application
    {
        return Application::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'pic_name' => $validated['pic_name'],
        ]);
    }

    // Function untuk mengecek apakah aplikasi sudah ada di database
    public function checkApplicationExists(string $name): bool
    {
        return Application::where('name', $name)->exists();
    }

    

    //Untuk mengatur format response aplikasi
    public function formatApplicationResponse(Application $application, ?string $secretKey = null): array
    {
        $response = [
            'application' => [
                'id' => $application->id,
                'name' => $application->name,
                'description' => $application->description,
                'pic_name' => $application->pic_name,
                'status' => $application->status,
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

    public function requestRegenerateSecretKey($id)
    {
        try {
            // Mencari aplikasi berdasarkan ID
            $application = Application::find($id);
    
            if (!$application) {
                return errorResponse('Application not found', 404);
            }
    
            // Pastikan status aplikasi  pending 
            if ($application->status === 'pending') {
                return errorResponse('Secret key regeneration request already in progress', 400);
            }
    
            // Update status aplikasi menjadi 'pending'
            $application->status = 'pending';
            $application->save();
    
            // Kembalikan response sukses
            return responseSuccess('Secret key regeneration request is now pending.');
        } catch (\Exception $e) {
            // Kembalikan response error
            return errorResponse('An error occurred while processing the request.', 500);
        }
    }
    
    public function approveGenerateSecretKey($id)
    {
        try {
            // Mencari aplikasi berdasarkan ID
            $application = Application::find($id);
    
            if (!$application) {
                return errorResponse('Application not found', 404);
            }
    
            // Pastikan status aplikasi pending
            if ($application->status !== 'pending') {
                return errorResponse('Invalid status for approval', 400);
            }
    
            // Generate secret key baru
            $SecretKey = Application::generateSecretKey();
    
            // Update secret key aplikasi 
            $application->secret_key = $SecretKey;
            // Update status aplikasi menjadi 'enabled'
            $application->status = 'enabled';
            
            $application->save();
    
            // Kembalikan response sukses
            return responseWithData('Approved successfully.', ['secret_key' => $SecretKey]);
        } catch (\Exception $e) {
            // Kembalikan response error
            return errorResponse('An error occurred while approving the secret key.', 500);
        }
    }
    
    public function rejectApplication($id)
    {
        try {
            // Mencari aplikasi berdasarkan ID
            $application = Application::find($id);
    
            if (!$application) {
                return errorResponse('Application not found', 404);
            }
    
            // Pastikan status aplikasi pending
            if ($application->status !== 'pending') {
                return errorResponse('Invalid status for rejection', 400);
            }
    
            // hapus aplikasi yang di rejected
            $this->deleteApplications([$id]);
    
            // Kembalikan response sukses
            return responseSuccess('Application has been rejected.');
        } catch (\Exception $e) {
            // Kembalikan response error
            return errorResponse('An error occurred while rejecting/disabling the secret key.', 500);
        }
    }
    
    public function changeStatusToEnabled($id)
    {
        try {
            // Mencari aplikasi berdasarkan ID
            $application = Application::find($id);
    
            if (!$application) {
                return errorResponse('Application not found', 404);
            }
    
            // Pastikan status aplikasi disabled
            if ($application->status !== 'disabled') {
                return errorResponse('Invalid status for change', 400);
            }
    
            // Update status aplikasi menjadi 'enabled'
            $application->status = 'enabled';
            
            $application->save();
    
            // Kembalikan response sukses
            return responseSuccess('Application status is enabled.');
        } catch (\Exception $e) {
            // Kembalikan response error
            return errorResponse('An error occurred while enabling the app.', 500);
        }
    }
    
    public function changeStatusToDisabled($id)
    {
        try {
            // Mencari aplikasi berdasarkan ID
            $application = Application::find($id);
    
            if (!$application) {
                return errorResponse('Application not found', 404);
            }
    
            // Pastikan status aplikasi enabled
            if ($application->status !== 'enabled') {
                return errorResponse('Invalid status for change', 400);
            }
    
            // Update status aplikasi menjadi 'disabled'
            $application->status = 'disabled';
            
            $application->save();
    
            // Kembalikan response sukses
            return responseSuccess('Application status is disabled.');
        } catch (\Exception $e) {
            // Kembalikan response error
            return errorResponse('An error occurred while disabling the app.', 500);
        }
    }
    
    public function rejectRegenerateSecretKey($id)
    {
        try {
            // Mencari aplikasi berdasarkan ID
            $application = Application::find($id);
    
            if (!$application) {
                return errorResponse('Application not found', 404);
            }
    
            // Pastikan status aplikasi pending
            if ($application->status !== 'pending') {
                return errorResponse('Invalid status for rejection', 400);
            }
    
            // Update status aplikasi menjadi 'disabled'
            $application->status = 'disabled';
            
            $application->save();
    
            // Kembalikan response sukses
            return responseSuccess('Regenerate request has been rejected.');
        } catch (\Exception $e) {
            // Kembalikan response error
            return errorResponse('An error occurred while rejecting the request.', 500);
        }
    }
}