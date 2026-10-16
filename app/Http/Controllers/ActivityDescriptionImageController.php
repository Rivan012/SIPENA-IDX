<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivityDescriptionImageController extends Controller
{
    public function storeForUser(Request $request): JsonResponse
    {
        return $this->store($request, 'users', (int) Auth::guard('web')->id());
    }

    public function storeForAdmin(Request $request): JsonResponse
    {
        return $this->store($request, 'admins', (int) Auth::guard('admin')->id());
    }

    public function show(Request $request, string $ownerType, int $ownerId, string $filename): StreamedResponse
    {
        abort_unless(in_array($ownerType, ['users', 'admins'], true), 404);
        abort_unless(preg_match('/^[a-f0-9-]+\.(jpg|png|webp)$/i', $filename), 404);

        $path = "description-images/{$ownerType}/{$ownerId}/{$filename}";
        abort_unless(Storage::disk('local')->exists($path), 404);
        abort_unless($this->canView($request, $ownerType, $ownerId, $path), 403);

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

    private function store(Request $request, string $ownerType, int $ownerId): JsonResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'extensions:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $image = $validated['image'];
        $extension = match ($image->getMimeType()) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => abort(422, 'Format gambar tidak didukung.'),
        };
        $filename = Str::uuid().'.'.$extension;
        $path = "description-images/{$ownerType}/{$ownerId}/{$filename}";

        abort_unless($image->storeAs(dirname($path), $filename, 'local'), 500);

        return response()->json([
            'src' => route('description-images.show', [
                'ownerType' => $ownerType,
                'ownerId' => $ownerId,
                'filename' => $filename,
            ], false),
            'alt' => pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME),
        ], 201);
    }

    private function canView(Request $request, string $ownerType, int $ownerId, string $path): bool
    {
        $viewer = $request->user();

        if ($viewer instanceof Admin) {
            return true;
        }

        if ($ownerType === 'users' && $viewer instanceof User && (int) $viewer->id === $ownerId) {
            return true;
        }

        return Kegiatan::query()
            ->whereIn('status', ['dipublikasikan', 'berlangsung', 'selesai'])
            ->where('deskripsi', 'like', '%'.$path.'%')
            ->exists();
    }
}
