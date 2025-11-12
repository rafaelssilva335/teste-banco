<?php

namespace Infrastructure\Http\Responses;

use Core\Domain\Entities\Account;

class AccountResponse
{
    public static function fromAccount(Account $account): array
    {
        return [
            'id' => $account->getId(),
            'balance' => $account->getBalance()
        ];
    }

    public static function depositResponse(Account $account): array
    {
        return [
            'destination' => self::fromAccount($account)
        ];
    }

    public static function withdrawResponse(Account $account): array
    {
        return [
            'origin' => self::fromAccount($account)
        ];
    }

    public static function transferResponse(Account $originAccount, Account $destinationAccount): array
    {
        return [
            'origin' => self::fromAccount($originAccount),
            'destination' => self::fromAccount($destinationAccount)
        ];
    }
}

