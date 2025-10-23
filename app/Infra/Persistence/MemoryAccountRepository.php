<?php

namespace App\Infra\Persistence;

use App\Domain\Repositories\AccounteRepository;

class MemoryAccountRepository implements AccounteRepository
{
    private $accounts = [];

    public function findById(string $id): Accounte
    {
        return $this->accounts[$id];
    }

    public function save(Accounte $accounte): void
    {
        $this->accounts[$accounte->getId()] = $accounte;
    }

    public function clear(): void
    {
        $this->accounts = [];
    }
}