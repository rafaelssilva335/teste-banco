<?php

namespace App\Domain\ValueObjects;

final class TransactionId
{
    private string $id;

    public function __construct(string $id = null)
    {
        $this->id = $id ?? uniqid('txn_');
    }

    public static function generate(): self
    {
        return new self();
    }

    public static function fromString(string $id): self
    {
        return new self($id);
    }

    public function getValue(): string
    {
        return $this->id;
    }

    public function equals(TransactionId $other): bool
    {
        return $this->id === $other->id;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
