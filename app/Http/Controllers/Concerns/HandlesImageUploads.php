<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Shared helpers for admin forms that let the admin upload a real image from
 * their computer instead of typing a placeholder "seed" string. Uploaded
 * files are stored on the 'public' disk under nakheel-images/ and the
 * relative path is saved in the same string column the old seed lived in —
 * <x-nakheel.ph> automatically renders a real <img> for any value that
 * exists on disk, and falls back to the gradient placeholder otherwise.
 */
trait HandlesImageUploads
{
    /**
     * No display context on the site ever shows an image anywhere near
     * this large (the biggest is the property/project hero banner, well
     * under 1400px tall even at high-DPI) — this just stops a photo
     * uploaded straight off a phone/camera (often 3000px+ on the long
     * edge) from being stored, and downloaded by every visitor, at its
     * full original resolution for no visible benefit.
     */
    private const MAX_DIMENSION = 1920;

    /**
     * Every upload is decoded and re-encoded before it's written to disk —
     * not just moved as-is. This is on top of the `image`/`mimes` request
     * validation the calling controller already runs: validation checks
     * the file LOOKS like an allowed image type, this step guarantees the
     * stored bytes ARE nothing but pixel data, since anything smuggled
     * past validation (e.g. a polyglot file) simply fails to decode here
     * and gets rejected rather than ever reaching the public disk.
     */
    protected function storeImage(UploadedFile $file): string
    {
        $image = $this->reencodeImage($file->getRealPath());
        $path = 'nakheel-images/'.Str::random(40).'.'.$image['extension'];

        Storage::disk('public')->put($path, $image['data']);

        return $path;
    }

    /** @return array{data: string, extension: string} */
    private function reencodeImage(string $path): array
    {
        $info = @getimagesize($path);
        abort_if($info === false, 422, 'The uploaded file is not a valid image.');

        $source = match ($info[2]) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_PNG => @imagecreatefrompng($path),
            IMAGETYPE_WEBP => @imagecreatefromwebp($path),
            default => false,
        };
        abort_if(! $source instanceof \GdImage, 422, 'The uploaded file is not a supported image type (JPEG, PNG, or WebP only).');

        // Preserve transparency for formats that support it.
        imagesavealpha($source, true);
        imagealphablending($source, true);

        $source = $this->capDimensions($source);

        ob_start();
        $extension = match ($info[2]) {
            IMAGETYPE_PNG => (imagepng($source, null, 6) ? 'png' : null),
            IMAGETYPE_WEBP => (imagewebp($source, null, 95) ? 'webp' : null),
            default => (imagejpeg($source, null, 95) ? 'jpg' : null),
        };
        $data = ob_get_clean();
        imagedestroy($source);

        abort_if($extension === null, 500, 'Failed to process the uploaded image.');

        return ['data' => $data, 'extension' => $extension];
    }

    /**
     * Scales an image down, preserving aspect ratio, if its largest
     * dimension exceeds self::MAX_DIMENSION — otherwise returns it
     * untouched. Destroys the original source once it's no longer needed.
     */
    private function capDimensions(\GdImage $source): \GdImage
    {
        $width = imagesx($source);
        $height = imagesy($source);
        $largest = max($width, $height);

        if ($largest <= self::MAX_DIMENSION) {
            return $source;
        }

        $scale = self::MAX_DIMENSION / $largest;
        $newWidth = max(1, (int) round($width * $scale));
        $newHeight = max(1, (int) round($height * $scale));

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagefill($resized, 0, 0, imagecolorallocatealpha($resized, 0, 0, 0, 127));

        imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($source);

        return $resized;
    }

    protected function deleteImageIfStored(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Resolve a single-image field: a new upload replaces (and deletes) the
     * old file, a checked "remove" checkbox clears it, otherwise the
     * existing value is kept untouched.
     */
    protected function resolveImagePath(Request $request, string $field, ?string $existing): ?string
    {
        if ($request->hasFile($field)) {
            $this->deleteImageIfStored($existing);

            return $this->storeImage($request->file($field));
        }

        if ($request->boolean($field.'_remove')) {
            $this->deleteImageIfStored($existing);

            return null;
        }

        return $existing;
    }

    /**
     * Resolve a comma-separated list-of-images field (e.g. hero_images):
     * unchecked "keep" checkboxes drop images (and delete their files),
     * newly uploaded files are appended to whatever remains.
     */
    protected function resolveImageList(Request $request, string $field, ?string $existingCsv): ?string
    {
        $existing = collect(explode(',', $existingCsv ?? ''))->map(fn ($v) => trim($v))->filter()->values();

        if ($request->has($field.'_keep_present')) {
            $keep = collect($request->input($field.'_keep', []));
            $existing->diff($keep)->each(fn ($path) => $this->deleteImageIfStored($path));
            $existing = $keep;
        }

        $newPaths = collect($request->file($field) ?? [])
            ->filter(fn ($file) => $file instanceof UploadedFile && $file->isValid())
            ->map(fn ($file) => $this->storeImage($file));

        $merged = $existing->merge($newPaths)->filter()->values();

        return $merged->isEmpty() ? null : $merged->implode(',');
    }
}
