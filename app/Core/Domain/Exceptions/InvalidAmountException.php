<?php

namespace Core\Domain\Exceptions;

class InvalidAmountException extends DomainException
{
    public function __construct(string $message = 'Amount must be greater than 0')
    {
        parent::__construct($message);
    }
}

