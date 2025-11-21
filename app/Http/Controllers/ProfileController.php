<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        // Deteksi user yang login
        if (Auth::guard('pengurus')->check()) {
            $user = Auth::guard('pengurus')->user();
            $guard = 'pengurus';
            $layout = 'layouts.app'; // Layout Admin
        } else {
            $user = Auth::guard('jamaah')->user();
            $guard = 'jamaah';
            $layout = 'layouts.public'; // Layout Public
        }

        return view('profile.edit', compact('user', 'guard', 'layout'));
    }

    public function update(Request $request)
    {
        // 1. Tentukan Guard & User
        if (Auth::guard('pengurus')->check()) {
            /** @var \App\Models\Pengurus $user */
            $user = Auth::guard('pengurus')->user();
            $table = 'pengurus';
        } else {
            /** @var \App\Models\Jamaah $user */
            $user = Auth::guard('jamaah')->user();
            $table = 'jamaah';
        }

        // 2. Validasi Dasar
        $rules = [
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|image|max:2048', // Max 2MB
        ];

        // 3. Validasi Email & Password (Hanya jika BUKAN login Google)
        if (!$user->google_id) {
            // Email harus unik, tapi abaikan ID user sendiri
            $rules['email'] = ['required', 'email', Rule::unique($table)->ignore($user->id)];
            
            // Password opsional (hanya jika diisi)
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }

        $validatedData = $request->validate($rules);

        // 4. Upload Foto (Jika ada)
        if ($request->hasFile('avatar')) {
            // Hapus foto lama jika ada
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validatedData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // 5. Update Password (Hanya jika diisi & bukan Google)
        if (!$user->google_id && $request->filled('password')) {
            $validatedData['password'] = Hash::make($request->password);
        } else {
            // Hapus key password dari array agar tidak menimpa dengan null/kosong
            unset($validatedData['password']);
        }

        // 6. Update Email (Hanya jika bukan Google)
        if ($user->google_id) {
            unset($validatedData['email']); // Cegah ganti email
        }

        // 7. Simpan Perubahan
        $user->update($validatedData);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}