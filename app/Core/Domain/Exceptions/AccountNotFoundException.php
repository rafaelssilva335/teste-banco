<?php

namespace Core\Domain\Exceptions;

class AccountNotFoundException extends DomainException
{
    public function __construct(string $accountId)
    {
        parent::__construct("Account not found: {$accountId}");
    }
}

