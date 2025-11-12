<?php

namespace Core\Domain\ValueObjects;

final class Money
{
    private float $amount;
    private string $currency;

    public function __construct(float $amount, string $currency = 'USD')
    {
        $this->validateAmount($amount);
        $this->amount = $amount;
        $this->currency = $currency;
    }

    private function validateAmount(float $amount): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function add(Money $money): Money
    {
        $this->ensureSameCurrency($money);
        return new Money($this->amount + $money->getAmount(), $this->currency);
    }

    public function subtract(Money $money): Money
    {
        $this->ensureSameCurrency($money);
        $newAmount = $this->amount - $money->getAmount();
        
        if ($newAmount < 0) {
            throw new \DomainException('Insufficient funds');
        }
        
        return new Money($newAmount, $this->currency);
    }

    public function equals(Money $money): bool
    {
        return $this->amount === $money->getAmount() && 
               $this->currency === $money->getCurrency();
    }

    private function ensureSameCurrency(Money $money): void
    {
        if ($this->currency !== $money->getCurrency()) {
            throw new \DomainException("Cannot operate on different currencies: {$this->currency} and {$money->getCurrency()}");
        }
    }

    public function __toString(): string
    {
        return sprintf('%.2f %s', $this->amount, $this->currency);
    }
}
