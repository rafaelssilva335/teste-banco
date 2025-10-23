<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Accounte;

interface AccounteRepository
{
    public function findById(string $id): Accounte;
    public function save(Accounte $accounte): void;
    public function clear(): void;
}