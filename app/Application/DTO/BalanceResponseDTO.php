<?php

namespace App\Application\DTO;

class BalanceResponseDTO
{
    private float $balance;

    public function __construct(float $balance)
    {
        $this->balance = $balance;
    }

    public function toArray(): array
    {
        return [
            'balance' => $this->balance
        ];
    }
}
