<?php

namespace App\Services;

class TemporaryPasswordService
{
    private const SYMBOLS = '!@#$%&*+-=?';

    public function generate(int $length = 20): string
    {
        $characters = [
            $this->pick('ABCDEFGHJKLMNPQRSTUVWXYZ'),
            $this->pick('abcdefghijkmnopqrstuvwxyz'),
            $this->pick('23456789'),
            $this->pick(self::SYMBOLS),
        ];
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789'.self::SYMBOLS;

        while (count($characters) < max(12, $length)) {
            $characters[] = $this->pick($alphabet);
        }

        for ($index = count($characters) - 1; $index > 0; $index--) {
            $swap = random_int(0, $index);
            [$characters[$index], $characters[$swap]] = [$characters[$swap], $characters[$index]];
        }

        return implode('', $characters);
    }

    private function pick(string $characters): string
    {
        return $characters[random_int(0, strlen($characters) - 1)];
    }
}
