<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class WorkoutTemplateItem extends Model
{
    protected $fillable=['exercise_id','position','sets','repetitions','rest_seconds'];
    public function template(){return $this->belongsTo(WorkoutTemplate::class,'workout_template_id');}
    public function exercise(){return $this->belongsTo(Exercise::class);}
}
