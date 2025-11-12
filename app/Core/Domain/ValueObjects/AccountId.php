<?php

namespace Core\Domain\ValueObjects;

final class AccountId
{
    private string $value;

    public function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        if (empty(trim($value))) {
            throw new \InvalidArgumentException('Account ID cannot be empty');
        }
    }

    public function equals(AccountId $other): bool
    {
        return $this->value === $other->value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

