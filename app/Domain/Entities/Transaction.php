<?php

namespace App\Domain\Entities;

use App\Domain\ValueObjects\TransactionId;
use App\Domain\ValueObjects\Money;
use DateTimeImmutable;

class Transaction
{
    private TransactionId $id;
    private string $sourceAccountId;
    private string $destinationAccountId;
    private Money $amount;
    private DateTimeImmutable $createdAt;
    private string $type;

    const TYPE_DEPOSIT = 'deposit';
    const TYPE_WITHDRAW = 'withdraw';
    const TYPE_TRANSFER = 'transfer';

    private function __construct(
        TransactionId $id,
        string $sourceAccountId,
        string $destinationAccountId,
        Money $amount,
        string $type,
        ?DateTimeImmutable $createdAt = null
    ) {
        $this->id = $id;
        $this->sourceAccountId = $sourceAccountId;
        $this->destinationAccountId = $destinationAccountId;
        $this->amount = $amount;
        $this->type = $type;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
    }

    public static function createDeposit(string $accountId, Money $amount): self
    {
        return new self(
            TransactionId::generate(),
            'system',
            $accountId,
            $amount,
            self::TYPE_DEPOSIT
        );
    }

    public static function createWithdrawal(string $accountId, Money $amount): self
    {
        return new self(
            TransactionId::generate(),
            $accountId,
            'system',
            $amount,
            self::TYPE_WITHDRAW
        );
    }

    public static function createTransfer(string $sourceAccountId, string $destinationAccountId, Money $amount): self
    {
        return new self(
            TransactionId::generate(),
            $sourceAccountId,
            $destinationAccountId,
            $amount,
            self::TYPE_TRANSFER
        );
    }

    public function getId(): TransactionId
    {
        return $this->id;
    }

    public function getSourceAccountId(): string
    {
        return $this->sourceAccountId;
    }

    public function getDestinationAccountId(): string
    {
        return $this->destinationAccountId;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
