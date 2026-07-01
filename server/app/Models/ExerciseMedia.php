<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseMedia extends Model
{
    protected $table = 'exercise_media';
    protected $fillable = ['type', 'path', 'url', 'provider', 'sort_order'];
    protected $appends = ['public_url'];

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    public function getPublicUrlAttribute(): ?string
    {
        return $this->type === 'image' && $this->path ? asset('storage/'.$this->path) : $this->url;
    }
}
