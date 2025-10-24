<?php

namespace App\Infra\Persistence;

use App\Domain\Entities\Transaction;
use App\Domain\Repositories\TransactionRepository;
use App\Domain\ValueObjects\TransactionId;

class MemoryTransactionRepository implements TransactionRepository
{
    private array $transactions = [];

    public function save(Transaction $transaction): void
    {
        $this->transactions[$transaction->getId()->getValue()] = $transaction;
    }

    public function findById(TransactionId $id): ?Transaction
    {
        $key = $id->getValue();
        return $this->transactions[$key] ?? null;
    }

    public function findByAccountId(string $accountId): array
    {
        return array_filter(
            $this->transactions,
            function (Transaction $transaction) use ($accountId) {
                return $transaction->getSourceAccountId() === $accountId || 
                       $transaction->getDestinationAccountId() === $accountId;
            }
        );
    }

    public function findBySourceAccountId(string $accountId): array
    {
        return array_filter(
            $this->transactions,
            function (Transaction $transaction) use ($accountId) {
                return $transaction->getSourceAccountId() === $accountId;
            }
        );
    }

    public function findByDestinationAccountId(string $accountId): array
    {
        return array_filter(
            $this->transactions,
            function (Transaction $transaction) use ($accountId) {
                return $transaction->getDestinationAccountId() === $accountId;
            }
        );
    }
}
