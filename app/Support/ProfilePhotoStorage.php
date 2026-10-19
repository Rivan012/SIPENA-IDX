<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfilePhotoStorage
{
    public function store(UploadedFile $photo, string $ownerType, int $ownerId): string
    {
        $extension = match ($photo->getMimeType()) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => abort(422, 'Format foto profil tidak didukung.'),
        };
        $filename = Str::uuid().'.'.$extension;
        $path = "profile-photos/{$ownerType}/{$ownerId}/{$filename}";

        abort_unless($photo->storeAs(dirname($path), $filename, 'local'), 500);

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path !== null) {
            Storage::disk('local')->delete($path);
        }
    }

    public function url(?string $path, string $ownerType, int $ownerId): ?string
    {
        if ($path === null) {
            return null;
        }

        return route('profile-photos.show', [
            'ownerType' => $ownerType,
            'ownerId' => $ownerId,
            'filename' => basename($path),
        ], false);
    }
}
