<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{

    // Function untuk menampilkan halaman profil pengguna
    public function edit()
    {
        return Inertia::render('Profile');
    }

    // Function untuk mengupdate profil pengguna
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->getKey()],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Alamat email tidak valid.',
            'email.unique' => 'Alamat email sudah digunakan.',
        ]);

        $user->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diupdate.',
                'user' => $user,
            ]);
        }

        return back()->with('success', 'Profil berhasil diupdate.');
    }

    // Function untuk mengupdate password pengguna
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8'],
            'new_password_confirmation' => ['required', 'string'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal harus minimal 8 karakter.',
            'new_password_confirmation.required' => 'Konfirmasi password baru wajib diisi.',
        ]);

        $user = $request->user();

        // Cek password saat ini
        if (!Hash::check($validated['current_password'], $user->password)) {
            $message = 'Password yang diisi tidak sama dengan password saat ini.';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'current_password' => [$message],
                    ],
                ], 422);
            }
            throw ValidationException::withMessages([
                'current_password' => $message,
            ]);
        }

        // Password baru tidak boleh sama dengan password saat ini
        if (Hash::check($validated['new_password'], $user->password)) {
            $message = 'Password baru tidak boleh sama dengan password saat ini.';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'new_password' => [$message],
                    ],
                ], 422);
            }
            throw ValidationException::withMessages([
                'new_password' => $message,
            ]);
        }

        // Konfirmasi password baru harus sama
        if ($validated['new_password'] !== $validated['new_password_confirmation']) {
            $message = 'Konfirmasi password baru tidak cocok.';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'new_password_confirmation' => [$message],
                    ],
                ], 422);
            }
            throw ValidationException::withMessages([
                'new_password_confirmation' => $message,
            ]);
        }

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diupdate.',
            ]);
        }

        return back()->with('success', 'Password berhasil diupdate.');
    }
}