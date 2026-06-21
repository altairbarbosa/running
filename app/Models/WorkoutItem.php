<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutItem extends Model
{
    protected $fillable = ['exercise_id', 'position', 'sets', 'repetitions', 'weight', 'rest_seconds', 'notes'];

    protected function casts(): array
    {
        return ['weight' => 'decimal:2'];
    }

    public function workout()
    {
        return $this->belongsTo(Workout::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}
