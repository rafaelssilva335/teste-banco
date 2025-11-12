<?php

namespace Core\Domain\Exceptions;

class SameAccountTransferException extends DomainException
{
    public function __construct(string $accountId)
    {
        parent::__construct("Cannot transfer to the same account: {$accountId}");
    }
}

