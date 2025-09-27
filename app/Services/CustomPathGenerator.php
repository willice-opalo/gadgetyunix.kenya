<?php

namespace App\Services;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        // Define the path where files are stored
        // return 'custom-path/' . $media->id . '/';
        return md5($media->id . config('app.key')) . '/';
    }

    public function getPathForConversions(Media $media): string
    {
        // Define the path for conversions (e.g., thumbnails)
        // return 'custom-path/' . $media->id . '/conversions/';
        return md5($media->id . config('app.key')) . '/conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        // Define the path for responsive images
        // return 'custom-path/' . $media->id . '/responsive/';
        return md5($media->id . config('app.key')) . '/responsive-images/';
    }
}
