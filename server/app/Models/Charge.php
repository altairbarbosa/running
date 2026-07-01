<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    protected $fillable = ['membership_id', 'type', 'description', 'due_date', 'amount', 'discount', 'late_fee', 'paid_amount', 'status'];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'amount' => 'decimal:2',
            'discount' => 'decimal:2',
            'late_fee' => 'decimal:2',
            'paid_amount' => 'decimal:2',
        ];
    }

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class)->latest('paid_at');
    }

    public function getTotalAttribute(): string
    {
        return bcsub(bcadd($this->amount, $this->late_fee, 2), $this->discount, 2);
    }

    public function getOutstandingAttribute(): string
    {
        return bcsub($this->total, $this->paid_amount, 2);
    }
}
