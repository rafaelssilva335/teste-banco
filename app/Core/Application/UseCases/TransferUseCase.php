<?php

namespace Core\Application\UseCases;

use Core\Domain\Entities\Account;
use Core\Domain\Exceptions\AccountNotFoundException;
use Core\Domain\Repositories\AccountRepository;

class TransferUseCase
{
    private AccountRepository $accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function execute(string $originAccountId, string $destinationAccountId, float $amount): array
    {
        $originAccount = $this->accountRepository->findById($originAccountId);
        
        if (!$originAccount) {
            throw new AccountNotFoundException($originAccountId);
        }

        $destinationAccount = $this->accountRepository->findById($destinationAccountId) ?? new Account($destinationAccountId);
        
        $originAccount->withdraw($amount);
        $destinationAccount->deposit($amount);
        $this->accountRepository->save($originAccount);
        $this->accountRepository->save($destinationAccount);
        
        return [
            'origin' => $originAccount,
            'destination' => $destinationAccount
        ];
    }
}

