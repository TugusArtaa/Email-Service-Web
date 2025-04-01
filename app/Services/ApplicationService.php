<?php

namespace App\Services;

use App\Models\Application;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ApplicationService
{
    // Method untuk mendapatkan aplikasi dalam database
    public function getAllApplications(): Collection
    {
        return Application::select('id', 'name', 'description', 'created_at')->get();
    }

    // Function untuk mendapatkan aplikasi berdasarkan ID 
    public function getPaginatedApplications(
        int $perPage = 10,
        string $orderBy = 'id',
        string $orderDirection = 'desc',
        string $search = ''
    ): LengthAwarePaginator {
        return Application::select('id', 'name', 'pic_name', 'secret_key', 'created_at', 'status')
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('pic_name', 'like', "%$search%");
            })
            ->whereIn('status', ['enabled', 'disabled'])
            ->orderBy($orderBy, $orderDirection)
            ->paginate($perPage);
    }

    // Function untuk mendapatkan aplikasi berdasarkan ID
    public function getPaginatedApprove(int $perPage = 10, string $orderBy = 'id', string $orderDirection = 'desc', string $search = ''): LengthAwarePaginator
    {
        return Application::select('id', 'name', 'pic_name', 'secret_key', 'created_at', 'status')
            ->where('name', 'like', "%$search%")
            ->whereIn('status', ['pending', 'request register'])
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

    public function approveGenerateSecretKey($id)
    {
        try {
            // Mencari aplikasi berdasarkan ID
            $application = Application::find($id);

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            // Pastikan status aplikasi pending atau register
            if ($application->status !== 'pending' && $application->status !== 'request register') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status for approval'
                ], 400);
            }

            // Generate secret key baru
            $SecretKey = Application::generateSecretKey();

            // Update secret key aplikasi 
            $application->secret_key = $SecretKey;
            // Update status aplikasi menjadi 'enabled'
            $application->status = 'enabled';

            $application->save();

            // Kembalikan response sukses
            return response()->json([
                'success' => true,
                'message' => 'Approved successfully.',
                'data' => [
                    'secret_key' => $SecretKey
                ]
            ]);
        } catch (\Exception $e) {
            // Kembalikan response error
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while approving the secret key.'
            ], 500);
        }
    }

    //Menangani perubahan status applikasi
    public function handleApplicationStatusChange($id, $status)
    {
        try {
            // Mencari aplikasi berdasarkan ID
            $application = Application::find($id);

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            switch ($status) {
                //Jika status pending, metode ini memeriksa apakah status aplikasi saat ini sudah pending.
                // Jika sudah, metode ini mengembalikan respons kesalahan yang menunjukkan bahwa permintaan regenerasi kunci rahasia sudah dalam proses.
                // Jika tidak, metode ini memperbarui status aplikasi menjadi pending dan menyimpan perubahan tersebut.
                case 'pending':
                    if ($application->status === 'pending') {
                        return response()->json([
                            'success' => false,
                            'message' => 'Secret key regeneration request already in progress'
                        ], 400);
                    }
                    $application->status = 'pending';
                    $application->save();

                    return response()->json([
                        'success' => true,
                        'message' => 'Secret key regeneration request is now pending.'
                    ]);
                // Jika status enabled, metode ini memeriksa apakah status aplikasi saat ini adalah disabled.
                // Jika tidak, metode ini mengembalikan respons kesalahan yang menunjukkan status tidak valid untuk perubahan.
                // Jika ya, metode ini memperbarui status aplikasi menjadi enabled 
                case 'enabled':
                    if ($application->status !== 'disabled') {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid status for change'
                        ], 400);
                    }
                    $application->status = 'enabled';
                    $application->save();
                    return response()->json([
                        'success' => true,
                        'message' => 'Application status is enabled.'
                    ]);
                // Jika status adalah disabled, metode ini memeriksa apakah status aplikasi saat ini adalah enabled atau pending.
                // Jika status saat ini adalah enabled, metode ini memperbarui status menjadi disabled 
                // Jika status saat ini adalah pending, metode ini memperbarui status menjadi disabled yang menunjukkan bahwa permintaan regenerasi telah ditolak.
                case 'disabled':
                    if ($application->status === 'enabled') {
                        $application->status = 'disabled';
                        $application->save();
                        return response()->json([
                            'success' => true,
                            'message' => 'Application status is disabled.'
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid status for change'
                        ], 400);
                    }
                // Jika status adalah rejected, memeriksa apakah status aplikasi saat ini adalah pending dan apakah secret_key adalah null.
                // Jika salah satu kondisi tidak terpenuhi, metode ini mengembalikan respons kesalahan yang menunjukkan status tidak valid untuk penolakan.
                // Jika ya, metode ini menghapus aplikasi yang menunjukkan bahwa aplikasi telah ditolak.
                case 'rejected':
                    if ($application->status === 'pending' && $application->secret_key !== null) {
                        $application->status = 'enabled';
                        $application->save();
                        return response()->json([
                            'success' => true,
                            'message' => 'Regenerate request has been rejected.'
                        ]);
                    }
                    if ($application->status !== 'request register' || $application->secret_key !== null) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid status for rejection'
                        ], 400);
                    }
                    $this->deleteApplications([$id]);
                    return response()->json([
                        'success' => true,
                        'message' => 'Application has been rejected.'
                    ]);
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid status'
                    ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the request.'
            ], 500);
        }
    }
}