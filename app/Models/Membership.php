<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    protected $fillable = ['member_id', 'plan_id', 'starts_at', 'ends_at', 'status', 'billing_day', 'price', 'notes'];

    protected function casts(): array
    {
        return [
            'starts_at' => 'date',
            'ends_at' => 'date',
            'price' => 'decimal:2',
        ];
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function charges()
    {
        return $this->hasMany(Charge::class)->orderByDesc('due_date');
    }
}
