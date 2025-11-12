<?php

namespace Core\Domain\Entities;

use Core\Domain\Exceptions\InsufficientBalanceException;
use Core\Domain\Exceptions\InvalidAmountException;
use Core\Domain\Exceptions\SameAccountTransferException;

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

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function deposit(float $amount): void
    {
        $this->assertValidAmount($amount);
        $this->balance += $amount;
    }

    public function withdraw(float $amount): void
    {
        $this->assertValidAmount($amount);
        $this->assertHasEnoughBalance($amount);
        $this->balance -= $amount;
    }

    public function transfer(float $amount, Account $account): void
    {
        $this->assertValidAmount($amount);
        $this->assertCanTransferTo($account);
        $this->assertHasEnoughBalance($amount);

        $this->withdraw($amount);
        $account->deposit($amount);
    }

    public function hasBalanceFor(float $amount): bool
    {
        return $this->balance >= $amount;
    }

    public function canTransferTo(Account $account): bool
    {
        return $account->getId() !== $this->id;
    }

    private function assertValidAmount(float $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidAmountException('Amount must be greater than 0');
        }
    }

    private function assertHasEnoughBalance(float $amount): void
    {
        if ($amount > $this->balance) {
            throw new InsufficientBalanceException($amount, $this->balance);
        }
    }

    private function assertCanTransferTo(Account $account): void
    {
        if (!$this->canTransferTo($account)) {
            throw new SameAccountTransferException($this->id);
        }
    }
}
