<?php

namespace App\Services;

use App\Models\Application;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ApplicationService
{
    // Function untuk mendapatkan semua aplikasi dengan paginasi 
    public function getPaginatedApplications(
        int $perPage = 10,
        string $orderBy = 'id',
        string $orderDirection = 'desc',
        string $search = ''
    ): LengthAwarePaginator {
        return Application::select('id', 'name', 'pic_name', 'created_at', 'status')
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('pic_name', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%");
            })
            ->whereIn('status', ['enabled', 'disabled'])
            ->orderBy($orderBy, $orderDirection)
            ->paginate($perPage);
    }

    // Function untuk mendapatkan aplikasi yang perlu disetujui (dengan paginasi)
    public function getPaginatedApprove(int $perPage = 10, string $orderBy = 'id', string $orderDirection = 'desc', string $search = ''): LengthAwarePaginator
    {
        return Application::select('id', 'name', 'pic_name', 'secret_key', 'created_at', 'status')
            ->where('name', 'like', "%$search%")
            ->whereIn('status', ['ubah-secret-key', 'registrasi-aplikasi'])
            ->orderBy($orderBy, $orderDirection)
            ->paginate($perPage);
    }

    // Function untuk membuat aplikasi baru
    public function createApplication(array $validated): Application
    {
        return Application::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'pic_name' => $validated['pic_name'],
        ]);
    }

    //Function untuk return format response aplikasi
    public function formatApplicationResponse(Application $application, ?string $secretKey = null): array
    {
        // Ubah agar response langsung array aplikasi (bukan nested 'application')
        return [
            'id' => $application->id,
            'name' => $application->name,
            'description' => $application->description,
            'pic_name' => $application->pic_name,
            'created_at' => $application->created_at,
            'secret_key' => $application->secret_key
        ];
    }

    // Function untuk menghapus aplikasi berdasarkan ID
    public function deleteApplications(array $ids): void
    {
        Application::whereIn('id', $ids)->delete();
    }

    // Function untuk approval regenerate secret key 
    public function approveGenerateSecretKey($id)
    {
        try {
            $application = Application::find($id);

            if (!$application) {
                return [
                    'success' => false,
                    'message' => 'Aplikasi tidak ditemukan',
                    'status' => 404
                ];
            }

            if ($application->status !== 'ubah-secret-key' && $application->status !== 'registrasi-aplikasi') {
                return [
                    'success' => false,
                    'message' => 'Status aplikasi tidak valid',
                    'status' => 400
                ];
            }

            $secretKey = Application::generateSecretKey();
            $application->secret_key = $secretKey;
            $application->status = 'enabled';
            $application->save();

            return [
                'success' => true,
                'message' => 'Approved sukses',
                'data' => [
                    'secret_key' => $secretKey
                ],
                'status' => 200
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses permintaan.',
                'status' => 500
            ];
        }
    }

    //Menangani perubahan status applikasi
    public function handleApplicationStatusChange($id, $status)
    {
        try {
            // Mencari aplikasi berdasarkan ID
            $application = Application::find($id);

            if (!$application) {
                return [
                    'success' => false,
                    'message' => 'Aplikasi tidak ditemukan',
                    'status' => 404
                ];
            }

            switch ($status) {
                //Jika status ubah-secret-key, metode ini memeriksa apakah status aplikasi saat ini sudah ubah-secret-key.
                // Jika sudah, metode ini mengembalikan respons kesalahan yang menunjukkan bahwa permintaan regenerasi kunci rahasia sudah dalam proses.
                // Jika tidak, metode ini memperbarui status aplikasi menjadi ubah-secret-key dan menyimpan perubahan tersebut.
                case 'ubah-secret-key':
                    if ($application->status === 'ubah-secret-key') {
                        return [
                            'success' => false,
                            'message' => 'Permintaan regenerasi secret key sudah dalam proses',
                            'status' => 400
                        ];
                    }
                    $application->status = 'ubah-secret-key';
                    $application->save();

                    return [
                        'success' => true,
                        'message' => 'Permintaan regenerasi secret key sedang diproses',
                        'status' => 200
                    ];
                // Jika status enabled, metode ini memeriksa apakah status aplikasi saat ini adalah disabled.
                // Jika tidak, metode ini mengembalikan respons kesalahan yang menunjukkan status tidak valid untuk perubahan.
                // Jika ya, metode ini memperbarui status aplikasi menjadi enabled 
                case 'enabled':
                    if ($application->status !== 'disabled') {
                        return [
                            'success' => false,
                            'message' => 'Status perubahan tidak valid',
                            'status' => 400
                        ];
                    }
                    $application->status = 'enabled';
                    $application->save();
                    return [
                        'success' => true,
                        'message' => 'Status aplikasi diubah menjadi enabled',
                        'status' => 200
                    ];
                // Jika status adalah disabled, metode ini memeriksa apakah status aplikasi saat ini adalah enabled atau ubah-secret-key.
                // Jika status saat ini adalah enabled, metode ini memperbarui status menjadi disabled 
                // Jika status saat ini adalah ubah-secret-key, metode ini memperbarui status menjadi disabled yang menunjukkan bahwa permintaan regenerasi telah ditolak.
                case 'disabled':
                    if ($application->status === 'enabled') {
                        $application->status = 'disabled';
                        $application->save();
                        return [
                            'success' => true,
                            'message' => 'Status aplikasi diubah menjadi disabled.',
                            'status' => 200
                        ];
                    } else {
                        return [
                            'success' => false,
                            'message' => 'Status perubahan tidak valid',
                            'status' => 400
                        ];
                    }
                // Jika status adalah rejected, memeriksa apakah status aplikasi saat ini adalah ubah-secret-key dan apakah secret_key adalah null.
                // Jika salah satu kondisi tidak terpenuhi, metode ini mengembalikan respons kesalahan yang menunjukkan status tidak valid untuk penolakan.
                // Jika ya, metode ini menghapus aplikasi yang menunjukkan bahwa aplikasi telah ditolak.
                case 'rejected':
                    if ($application->status === 'ubah-secret-key' && $application->secret_key !== null) {
                        $application->status = 'enabled';
                        $application->save();
                        return [
                            'success' => true,
                            'message' => 'Permintaan ditolak',
                            'status' => 200
                        ];
                    }
                    if ($application->status !== 'registrasi-aplikasi' || $application->secret_key !== null) {
                        return [
                            'success' => false,
                            'message' => 'Status tidak valid',
                            'status' => 400
                        ];
                    }
                    $this->deleteApplications([$id]);
                    return [
                        'success' => true,
                        'message' => 'Aplikasi telah ditolak',
                        'status' => 200
                    ];
                default:
                    return [
                        'success' => false,
                        'message' => 'Status tidak valid',
                        'status' => 400
                    ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses permintaan.',
                'status' => 500
            ];
        }
    }
}