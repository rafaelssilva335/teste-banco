<?php

namespace Infrastructure\Http\Controllers;

use Core\Application\UseCases\DepositUseCase;
use Core\Application\UseCases\WithdrawUseCase;
use Core\Application\UseCases\TransferUseCase;
use Core\Application\UseCases\GetBalanceUseCase;
use Core\Application\UseCases\ResetUseCase;
use Infrastructure\Http\Requests\GetBalanceFormRequest;
use Infrastructure\Http\Requests\EventFormRequest;
use Infrastructure\Http\Responses\AccountResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BankController extends Controller
{
    private ResetUseCase $resetUseCase;
    private GetBalanceUseCase $getBalanceUseCase;
    private DepositUseCase $depositUseCase;
    private WithdrawUseCase $withdrawUseCase;
    private TransferUseCase $transferUseCase;

    public function __construct(
        ResetUseCase $resetUseCase,
        GetBalanceUseCase $getBalanceUseCase,
        DepositUseCase $depositUseCase,
        WithdrawUseCase $withdrawUseCase,
        TransferUseCase $transferUseCase
    ) {
        $this->resetUseCase = $resetUseCase;
        $this->getBalanceUseCase = $getBalanceUseCase;
        $this->depositUseCase = $depositUseCase;
        $this->withdrawUseCase = $withdrawUseCase;
        $this->transferUseCase = $transferUseCase;
    }

    public function reset(): Response
    {
        $this->resetUseCase->execute();
        return response('OK', Response::HTTP_OK);
    }

    public function getBalance(Request $request): Response
    {
        $formRequest = GetBalanceFormRequest::createFromBase($request);
        $validated = $formRequest->validated();
        
        $balance = $this->getBalanceUseCase->execute($validated['account_id']);
        return AccountResponseFactory::balance($balance);
    }

    public function processEvent(Request $request): Response
    {
        $formRequest = EventFormRequest::createFromBase($request);
        $validated = $formRequest->validated();
        
        return match ($validated['type']) {
            'deposit' => $this->handleDeposit($validated),
            'withdraw' => $this->handleWithdraw($validated),
            'transfer' => $this->handleTransfer($validated),
            default => AccountResponseFactory::badRequest(),
        };
    }

    private function handleDeposit(array $validated): Response
    {
        $account = $this->depositUseCase->execute(
            $validated['destination'],
            (float) $validated['amount']
        );
        
        return AccountResponseFactory::deposit($account);
    }

    private function handleWithdraw(array $validated): Response
    {
        $account = $this->withdrawUseCase->execute(
            $validated['origin'],
            (float) $validated['amount']
        );
        
        return AccountResponseFactory::withdraw($account);
    }

    private function handleTransfer(array $validated): Response
    {
        $result = $this->transferUseCase->execute(
            $validated['origin'],
            $validated['destination'],
            (float) $validated['amount']
        );
        
        return AccountResponseFactory::transfer(
            $result['origin'],
            $result['destination']
        );
    }
}
