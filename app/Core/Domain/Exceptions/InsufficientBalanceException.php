<?php

namespace Core\Domain\Exceptions;

class InsufficientBalanceException extends DomainException
{
    public function __construct(float $requestedAmount, float $availableBalance)
    {
        $message = sprintf(
            'Insufficient balance. Requested: %.2f, Available: %.2f',
            $requestedAmount,
            $availableBalance
        );
        parent::__construct($message);
    }
}

