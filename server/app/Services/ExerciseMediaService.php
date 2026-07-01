<?php

namespace App\Services;

use App\Models\Exercise;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ExerciseMediaService
{
    public function update(Exercise $exercise, array $images = [], ?string $videoUrl = null, array $removeIds = []): void
    {
        $exercise->media()->whereIn('id', $removeIds)->get()->each(function ($media) {
            if ($media->path) {
                Storage::disk('public')->delete($media->path);
            }
            $media->delete();
        });

        collect($images)->filter(fn ($image) => $image instanceof UploadedFile)->each(function (UploadedFile $image) use ($exercise) {
            $exercise->media()->create([
                'type' => 'image',
                'path' => $image->store('exercises/'.$exercise->id, 'public'),
                'sort_order' => $exercise->media()->max('sort_order') + 10,
            ]);
        });

        if ($videoUrl) {
            $exercise->media()->create([
                'type' => 'video',
                'url' => $videoUrl,
                'provider' => $this->provider($videoUrl),
                'sort_order' => $exercise->media()->max('sort_order') + 10,
            ]);
        }
    }

    private function provider(string $url): string
    {
        $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');

        return match (true) {
            str_contains($host, 'youtube.com'), str_contains($host, 'youtu.be') => 'youtube',
            str_contains($host, 'vimeo.com') => 'vimeo',
            default => 'other',
        };
    }
}
