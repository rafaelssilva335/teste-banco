<?php

namespace Infrastructure\Http\Responses;

use Core\Domain\Entities\Account;
use Illuminate\Http\Response;

class AccountResponseFactory
{
    public static function deposit(Account $account): Response
    {
        return response(
            AccountResponse::depositResponse($account),
            Response::HTTP_CREATED
        );
    }

    public static function withdraw(Account $account): Response
    {
        return response(
            AccountResponse::withdrawResponse($account),
            Response::HTTP_CREATED
        );
    }

    public static function transfer(Account $origin, Account $destination): Response
    {
        return response(
            AccountResponse::transferResponse($origin, $destination),
            Response::HTTP_CREATED
        );
    }

    public static function balance(float $balance): Response
    {
        return response($balance, Response::HTTP_OK);
    }

    public static function notFound(): Response
    {
        return response('0', Response::HTTP_NOT_FOUND);
    }

    public static function badRequest(): Response
    {
        return response('', Response::HTTP_BAD_REQUEST);
    }

    public static function internalServerError(): Response
    {
        return response('', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
