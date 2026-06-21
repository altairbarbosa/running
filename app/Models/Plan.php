<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['name', 'price', 'enrollment_fee', 'billing_interval_months', 'description', 'active'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'enrollment_fee' => 'decimal:2',
            'active' => 'boolean',
        ];
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }
}
