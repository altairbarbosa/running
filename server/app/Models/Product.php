<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name','description','price','stock','image_path','active'];
    protected $appends = ['image_url'];
    protected function casts(): array { return ['price'=>'decimal:2','active'=>'boolean']; }
    public function getImageUrlAttribute(): ?string { return $this->image_path ? asset('storage/'.$this->image_path) : null; }
}
