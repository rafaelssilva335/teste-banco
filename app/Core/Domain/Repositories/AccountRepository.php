<?php

namespace Core\Domain\Repositories;

use Core\Domain\Entities\Account;

interface AccountRepository
{
    public function findById(string $id): ?Account;
    public function save(Account $account): void;
    public function clear(): void;
}