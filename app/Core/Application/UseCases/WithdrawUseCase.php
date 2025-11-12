<?php

namespace Core\Application\UseCases;

use Core\Domain\Entities\Account;
use Core\Domain\Exceptions\AccountNotFoundException;
use Core\Domain\Repositories\AccountRepository;

class WithdrawUseCase
{
    private AccountRepository $accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function execute(string $accountId, float $amount): Account
    {
        $account = $this->accountRepository->findById($accountId);
        if (!$account) {
            throw new AccountNotFoundException($accountId);
        }
        $account->withdraw($amount);
        $this->accountRepository->save($account);
        return $account;
    }
}

