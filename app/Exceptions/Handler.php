<?php

namespace App\Exceptions;

use Core\Domain\Exceptions\AccountNotFoundException;
use Core\Domain\Exceptions\InsufficientBalanceException;
use Core\Domain\Exceptions\InvalidAmountException;
use Core\Domain\Exceptions\SameAccountTransferException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use Infrastructure\Http\Responses\AccountResponseFactory;
use Infrastructure\Http\Exceptions\ExceptionMapper;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    public function render($request, Throwable $exception)
    {
        // Validação HTTP - retorna 400
        if ($exception instanceof ValidationException) {
            return AccountResponseFactory::badRequest();
        }

        // Exceções de domínio conhecidas
        if ($exception instanceof AccountNotFoundException || 
            $exception instanceof InsufficientBalanceException ||
            $exception instanceof InvalidAmountException ||
            $exception instanceof SameAccountTransferException) {
            return ExceptionMapper::toResponse($exception, $this->getEventType($request));
        }

        // Erros de PDO SQLite
        if ($exception instanceof \PDOException && strpos($exception->getMessage(), 'could not find driver') !== false) {
            return response()->json([
                'error' => 'PDO SQLite driver não está disponível',
                'message' => 'Instale o driver SQLite: sudo apt-get install php-sqlite3',
                'details' => $exception->getMessage()
            ], 500);
        }
        
        if ($exception instanceof \RuntimeException && strpos($exception->getMessage(), 'PDO SQLite') !== false) {
            return response()->json([
                'error' => 'PDO SQLite driver não está disponível',
                'message' => $exception->getMessage()
            ], 500);
        }

        // Erros inesperados - log e retorna 500
        error_log("Unexpected error: " . $exception->getMessage());
        error_log("Stack trace: " . $exception->getTraceAsString());
        
        return AccountResponseFactory::internalServerError();
    }

    private function getEventType($request): ?string
    {
        return $request->input('type');
    }
}
