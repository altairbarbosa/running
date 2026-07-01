<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['member_id','status','total','ordered_at'];
    protected function casts(): array { return ['total'=>'decimal:2','ordered_at'=>'datetime']; }
    public function member() { return $this->belongsTo(User::class, 'member_id'); }
    public function items() { return $this->hasMany(OrderItem::class); }
}
