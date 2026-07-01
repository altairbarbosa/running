<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AvatarService
{
    public function update(User $user, ?UploadedFile $avatar, bool $remove = false): void
    {
        if (! $avatar && ! $remove) {
            return;
        }

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->update([
            'avatar_path' => $avatar?->store('avatars', 'public'),
        ]);
    }
}
