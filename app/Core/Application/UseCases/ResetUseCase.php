<?php

namespace Core\Application\UseCases;

use Core\Domain\Repositories\AccountRepository;

class ResetUseCase
{
    private AccountRepository $accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function execute(): void
    {
        $this->accountRepository->clear();
    }
}

