<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! preg_match('/^(?=(?:\D*\d){8,15}\D*$)\+?[0-9() .-]+$/', $value)) {
            $fail('Informe um telefone válido, com 8 a 15 dígitos.');
        }
    }
}
