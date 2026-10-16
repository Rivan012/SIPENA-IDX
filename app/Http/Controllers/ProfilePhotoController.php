<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfilePhotoController extends Controller
{
    public function show(Request $request, string $ownerType, int $ownerId, string $filename): StreamedResponse
    {
        abort_unless(in_array($ownerType, ['users', 'admins'], true), 404);
        abort_unless(preg_match('/^[a-f0-9-]+\.(jpg|png|webp)$/i', $filename) === 1, 404);

        $owner = $ownerType === 'users'
            ? Auth::guard('web')->user()
            : Auth::guard('admin')->user();

        abort_unless(($owner instanceof User || $owner instanceof Admin) && (int) $owner->getAuthIdentifier() === $ownerId, 404);

        $path = "profile-photos/{$ownerType}/{$ownerId}/{$filename}";
        abort_unless(Storage::disk('local')->exists($path), 404);

        $mimeType = match (strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
        };

        return Storage::disk('local')->response($path, $filename, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
        ]);
    }
}
