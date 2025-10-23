<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Transaction;
use App\Domain\ValueObjects\TransactionId;

interface TransactionRepository
{
    public function save(Transaction $transaction): void;
    
    public function findById(TransactionId $id): ?Transaction;
    
    public function findByAccountId(string $accountId): array;
    
    public function findBySourceAccountId(string $accountId): array;
    
    public function findByDestinationAccountId(string $accountId): array;
}
