<?php

namespace App\Infra\Persistence;

use App\Domain\Repositories\AccountRepository;
use App\Domain\Entities\Account;

class MemoryAccountRepository implements AccountRepository
{
    private $accounts = [];

    public function findById(string $id): ?Account
    {
        return $this->accounts[$id] ?? null;
    }

    public function save(Account $account): void
    {
        $this->accounts[$account->getId()] = $account;
    }

    public function clear(): void
    {
        $this->accounts = [];
    }
}