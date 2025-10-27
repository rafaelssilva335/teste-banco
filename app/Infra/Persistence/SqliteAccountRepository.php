<?php

namespace App\Infra\Persistence;

use App\Domain\Repositories\AccountRepository;
use App\Domain\Entities\Account;
use PDO;

class SqliteAccountRepository implements AccountRepository
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
            CREATE TABLE IF NOT EXISTS accounts (
                id TEXT PRIMARY KEY,
                balance REAL NOT NULL
            )
        ");
    }
    
    public function findById(string $id): ?Account
    {
        $stmt = $this->pdo->prepare("SELECT id, balance FROM accounts WHERE id = :id");
        $stmt->execute(['id' => $id]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        
        return new Account($row['id'], (float)$row['balance']);
    }
    
    public function save(Account $account): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO accounts (id, balance) VALUES (:id, :balance)
            ON CONFLICT (id) DO UPDATE SET balance = :balance
        ");
        
        $stmt->execute([
            'id' => $account->getId(),
            'balance' => $account->getBalance()
        ]);
    }
    
    public function clear(): void
    {
        $this->pdo->exec("DELETE FROM accounts");
    }
}
