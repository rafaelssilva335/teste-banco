<?php

namespace Core\Application\UseCases;

use Core\Domain\Entities\Account;
use Core\Domain\Repositories\AccountRepository;

class GetAccountUseCase
{
    private AccountRepository $accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function execute(string $accountId): ?Account
    {
        return $this->accountRepository->findById($accountId);
    }
}

