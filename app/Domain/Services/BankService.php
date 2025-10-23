<?php

namespace App\Domain\Services;

use App\Domain\Repositories\AccountRepository;
use App\Domain\Entities\Account;

class BankService
{
    private $accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function reset(): void {
        $this->accountRepository->clear();
    }

    public function getBalance(string $accountId): float
    {
        $account = $this->accountRepository->findById($accountId);
        if (!$account) {
            throw new \DomainException('Account not found');
        }
        return $account->getBalance();
    }
    
    public function getAccountById(string $accountId): ?Account
    {
        return $this->accountRepository->findById($accountId);
    }

    public function deposit(string $accountId, float $amount): Account
    {
        $account = $this->accountRepository->findById($accountId) ?? new Account($accountId);
        $account->deposit($amount);
        $this->accountRepository->save($account);
        return $account;
    }

    public function withdraw(string $accountId, float $amount): Account
    {
        $account = $this->accountRepository->findById($accountId);
        if (!$account) {
            throw new \DomainException('Account not found');
        }
        $account->withdraw($amount);
        $this->accountRepository->save($account);
        return $account;
    }

    public function transfer(string $originAccountId, string $destinationAccountId, float $amount): ?Account
    {
        $originAccount = $this->accountRepository->findById($originAccountId);
        
        if(!$originAccount) {
            throw new \DomainException('Origin account not found');
        }

        $destinationAccount = $this->accountRepository->findById($destinationAccountId);
        
        if (!$destinationAccount) {
            throw new \DomainException('Destination account not found');
        }
        $originAccount->withdraw($amount);
        $destinationAccount->deposit($amount);
        $this->accountRepository->save($originAccount);
        $this->accountRepository->save($destinationAccount);
        return $originAccount;
    }

}