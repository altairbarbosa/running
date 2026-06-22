<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class WorkoutTemplate extends Model
{
    protected $fillable=['created_by','name','description','active'];
    protected function casts(): array{return['active'=>'boolean'];}
    public function items(){return $this->hasMany(WorkoutTemplateItem::class)->orderBy('position');}
    public function author(){return $this->belongsTo(User::class,'created_by');}
}
