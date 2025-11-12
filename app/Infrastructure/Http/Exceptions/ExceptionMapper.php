<?php

namespace Infrastructure\Http\Exceptions;

use Core\Domain\Exceptions\AccountNotFoundException;
use Core\Domain\Exceptions\InsufficientBalanceException;
use Illuminate\Http\Response;

class ExceptionMapper
{
    public static function toHttpStatusCode(\Throwable $exception): int
    {
        return match (true) {
            $exception instanceof AccountNotFoundException => Response::HTTP_NOT_FOUND,
            $exception instanceof InsufficientBalanceException => Response::HTTP_NOT_FOUND,
            default => Response::HTTP_INTERNAL_SERVER_ERROR,
        };
    }

    public static function toHttpResponse(\Throwable $exception, ?string $eventType = null): array|string
    {
        if ($exception instanceof AccountNotFoundException) {
            return match ($eventType) {
                'deposit' => ['destination' => ['id' => $exception->getAccountId(), 'balance' => 0]],
                default => '0',
            };
        }

        if ($exception instanceof InsufficientBalanceException) {
            return '0';
        }

        return '';
    }

    public static function toResponse(\Throwable $exception, ?string $eventType = null): Response
    {
        $statusCode = self::toHttpStatusCode($exception);
        $responseBody = self::toHttpResponse($exception, $eventType);

        if ($statusCode === Response::HTTP_NOT_FOUND && 
            in_array($eventType, ['withdraw', 'transfer'], true)) {
            return response('0', Response::HTTP_NOT_FOUND);
        }

        return response($responseBody, $statusCode);
    }
}
