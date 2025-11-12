<?php

namespace Core\Application\UseCases;

use Core\Domain\Exceptions\AccountNotFoundException;
use Core\Domain\Repositories\AccountRepository;

class GetBalanceUseCase
{
    private AccountRepository $accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function execute(string $accountId): float
    {
        $account = $this->accountRepository->findById($accountId);
        if (!$account) {
            throw new AccountNotFoundException($accountId);
        }
        return $account->getBalance();
    }
}

