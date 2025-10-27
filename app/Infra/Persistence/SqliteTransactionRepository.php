<?php

namespace App\Infra\Persistence;

use App\Domain\Entities\Transaction;
use App\Domain\Repositories\TransactionRepository;
use App\Domain\ValueObjects\TransactionId;
use App\Domain\ValueObjects\Money;
use PDO;

class SqliteTransactionRepository implements TransactionRepository
{
    private PDO $pdo;
    
    public function __construct()
    {
        $dbPath = storage_path('app/database.sqlite');
        $this->pdo = new PDO("sqlite:{$dbPath}");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->createTableIfNotExists();
    }
    
    private function createTableIfNotExists(): void
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS transactions (
                id TEXT PRIMARY KEY,
                source_account_id TEXT NOT NULL,
                destination_account_id TEXT NOT NULL,
                amount REAL NOT NULL,
                currency TEXT NOT NULL,
                type TEXT NOT NULL,
                created_at DATETIME NOT NULL
            )
        ");
    }

    public function save(Transaction $transaction): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO transactions 
                (id, source_account_id, destination_account_id, amount, currency, type, created_at) 
            VALUES 
                (:id, :source_account_id, :destination_account_id, :amount, :currency, :type, :created_at)
        ");
        
        $stmt->execute([
            'id' => $transaction->getId()->getValue(),
            'source_account_id' => $transaction->getSourceAccountId(),
            'destination_account_id' => $transaction->getDestinationAccountId(),
            'amount' => $transaction->getAmount()->getAmount(),
            'currency' => $transaction->getAmount()->getCurrency(),
            'type' => $transaction->getType(),
            'created_at' => $transaction->getCreatedAt()->format('Y-m-d H:i:s')
        ]);
    }
    
    public function findById(TransactionId $id): ?Transaction
    {
        // Nota: Esta é uma implementação simplificada.
        // Em um sistema real, seria necessário reconstruir completamente o objeto Transaction
        // com seus Value Objects, etc.
        return null;
    }
    
    public function findByAccountId(string $accountId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM transactions 
            WHERE source_account_id = :account_id OR destination_account_id = :account_id
        ");
        $stmt->execute(['account_id' => $accountId]);
        
        return [];  // Implementação simplificada
    }
    
    public function findBySourceAccountId(string $accountId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM transactions 
            WHERE source_account_id = :account_id
        ");
        $stmt->execute(['account_id' => $accountId]);
        
        return [];  // Implementação simplificada
    }
    
    public function findByDestinationAccountId(string $accountId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM transactions 
            WHERE destination_account_id = :account_id
        ");
        $stmt->execute(['account_id' => $accountId]);
        
        return [];  // Implementação simplificada
    }
}
