<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ProfilePhotoStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function __construct(private readonly ProfilePhotoStorage $profilePhotos) {}

    public function edit(): Response
    {
        $admin = Auth::guard('admin')->user();

        return Inertia::render('Admin/Profil/Edit', [
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'no_telepon' => $admin->no_telepon,
                'jabatan' => $admin->jabatan,
                'profile_photo_url' => $this->profilePhotos->url($admin->profile_photo_path, 'admins', $admin->id),
            ],
        ]);
    }

    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins,email,'.$admin->id],
            'no_telepon' => ['nullable', 'string', 'max:20'],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'extensions:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $oldPhotoPath = $admin->profile_photo_path;
        $admin->name = $validated['name'];
        $admin->email = $validated['email'];
        $admin->no_telepon = $validated['no_telepon'] ?? null;
        $admin->jabatan = $validated['jabatan'] ?? null;

        if (isset($validated['avatar'])) {
            $admin->profile_photo_path = $this->profilePhotos->store($validated['avatar'], 'admins', $admin->id);
        }

        $admin->save();

        if (isset($validated['avatar'])) {
            $this->profilePhotos->delete($oldPhotoPath);
        }

        return redirect()->route('admin.profil.edit')->with('success', 'Profil administrator berhasil diperbarui.');
    }
}
