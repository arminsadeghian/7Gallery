<?php

namespace App\Utils;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

final class ImageUploader
{
    public static function upload($image, string $path, string $diskType = 'local_storage'): void
    {
        if (!is_null($image)) {
            Storage::disk($diskType)->put($path, File::get($image));
        }
    }

    public static function uploadMany(array $images, string $path, string $diskType = 'public_storage'): array
    {
        $imagesPath = [];

        foreach ($images as $key => $image) {
            $fullPath = $path . $key . '_' . self::fileNameToHash($image);
            self::upload($image, $fullPath, $diskType);
            $imagesPath += [$key => $fullPath];
        }

        return $imagesPath;
    }

    public static function fileNameToHash($fileName): string
    {
        $fileNameSeparate = explode('.', $fileName->getClientOriginalName());
        $fileNameExtension = strtolower(end($fileNameSeparate));
        return sha1_file($fileName->getRealPath()) . '.' . $fileNameExtension;
    }

}
