<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workout extends Model
{
    protected $fillable = ['member_id', 'created_by', 'name', 'starts_at', 'ends_at', 'status', 'notes'];

    protected function casts(): array
    {
        return ['starts_at' => 'date', 'ends_at' => 'date'];
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(WorkoutItem::class)->orderBy('position');
    }
}
