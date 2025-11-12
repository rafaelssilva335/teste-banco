<?php

namespace Core\Application\UseCases;

use Core\Domain\Entities\Account;
use Core\Domain\Repositories\AccountRepository;

class DepositUseCase
{
    private AccountRepository $accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function execute(string $accountId, float $amount): Account
    {
        $account = $this->accountRepository->findById($accountId) ?? new Account($accountId);
        $account->deposit($amount);
        $this->accountRepository->save($account);
        return $account;
    }
}

