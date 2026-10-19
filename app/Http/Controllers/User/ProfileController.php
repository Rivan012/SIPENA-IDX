<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Support\ProfilePhotoStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function __construct(private readonly ProfilePhotoStorage $profilePhotos) {}

    public function edit(): Response
    {
        $user = Auth::guard('web')->user();

        return Inertia::render('User/Profil/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'no_telepon' => $user->no_telepon,
                'profile_photo_url' => $this->profilePhotos->url($user->profile_photo_path, 'users', $user->id),
            ],
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'no_telepon' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'extensions:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $oldPhotoPath = $user->profile_photo_path;
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->no_telepon = $validated['no_telepon'] ?? null;

        if (isset($validated['avatar'])) {
            $user->profile_photo_path = $this->profilePhotos->store($validated['avatar'], 'users', $user->id);
        }

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if (isset($validated['avatar'])) {
            $this->profilePhotos->delete($oldPhotoPath);
        }

        return redirect()->route('user.profil.edit')->with('success', 'Profil Anda berhasil diperbarui.');
    }
}
