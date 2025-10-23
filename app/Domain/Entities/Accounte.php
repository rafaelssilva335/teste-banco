<?php

namespace App\Domain\Entities;

class Account
{
    private string $id;
    private float $balance;

    public function __construct(
        string $id,
        float $balance = 0,
    ) {
        $this->id = $id;
        $this->balance = $balance;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function deposit(float $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be greater than 0');
        }
        $this->balance += $amount;
    }

    public function withdraw(float $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be greater than 0');
        }

        if ($amount > $this->balance) {
            throw new \DomainException('Insufficient balance');
        }

        $this->balance -= $amount;
    }

    public function transfer(float $amount, Account $account): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be greater than 0');
        }

        if ($amount > $this->balance) {
            throw new \DomainException('Insufficient balance');
        }

        if ($account->getId() === $this->getId()) {
            throw new \DomainException('Cannot transfer to the same account');
        }

        $this->withdraw($amount);
        $account->deposit($amount);
    }
}