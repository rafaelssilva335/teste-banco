<?php

namespace App\Http\Controllers;

use App\Domain\Services\BankService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BankController extends Controller
{
    private BankService $bankService;

    public function __construct(BankService $bankService)
    {
        $this->bankService = $bankService;
    }

    public function reset()
    {
        $this->bankService->reset();
        return response('OK', Response::HTTP_OK);
    }

    public function getBalance(Request $request)
    {
        $accountId = $request->query('account_id');
        
        if (!$accountId) {
            return response('', Response::HTTP_BAD_REQUEST);
        }
        
        try {
            $balance = $this->bankService->getBalance($accountId);
            return response($balance, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response('0', Response::HTTP_NOT_FOUND);
        }
    }

    public function processEvent(Request $request)
    {
        $data = $request->json()->all();
        $type = $data['type'] ?? null;

        if (!$type) {
            return response('', Response::HTTP_BAD_REQUEST);
        }

        try {
            switch ($type) {
                case 'deposit':
                    return $this->handleDeposit($data);
                case 'withdraw':
                    return $this->handleWithdraw($data);
                case 'transfer':
                    return $this->handleTransfer($data);
                default:
                    return response('', Response::HTTP_BAD_REQUEST);
            }
        } catch (\Exception $e) {
            if ($type === 'withdraw' || $type === 'transfer') {
                return response('0', Response::HTTP_NOT_FOUND);
            }
            return response('', Response::HTTP_BAD_REQUEST);
        }
    }

    private function handleDeposit(array $data)
    {
        $destination = $data['destination'] ?? null;
        $amount = $data['amount'] ?? null;

        if (!$destination || !$amount) {
            return response('', Response::HTTP_BAD_REQUEST);
        }

        $account = $this->bankService->deposit($destination, $amount);
        
        return response([
            'destination' => [
                'id' => $account->getId(),
                'balance' => $account->getBalance()
            ]
        ], Response::HTTP_CREATED);
    }

    private function handleWithdraw(array $data)
    {
        $origin = $data['origin'] ?? null;
        $amount = $data['amount'] ?? null;

        if (!$origin || !$amount) {
            return response('', Response::HTTP_BAD_REQUEST);
        }

        $account = $this->bankService->withdraw($origin, $amount);
        
        return response([
            'origin' => [
                'id' => $account->getId(),
                'balance' => $account->getBalance()
            ]
        ], Response::HTTP_CREATED);
    }

    private function handleTransfer(array $data)
    {
        $origin = $data['origin'] ?? null;
        $destination = $data['destination'] ?? null;
        $amount = $data['amount'] ?? null;

        if (!$origin || !$destination || !$amount) {
            return response('', Response::HTTP_BAD_REQUEST);
        }

        try {
            $result = $this->bankService->transfer($origin, $destination, $amount);
            
            $originAccount = $this->bankService->getAccountById($origin);
            $destinationAccount = $this->bankService->getAccountById($destination);
            
            return response([
                'origin' => [
                    'id' => $originAccount->getId(),
                    'balance' => $originAccount->getBalance()
                ],
                'destination' => [
                    'id' => $destinationAccount->getId(),
                    'balance' => $destinationAccount->getBalance()
                ]
            ], Response::HTTP_CREATED);
        } catch (\DomainException $e) {
            return response('0', Response::HTTP_NOT_FOUND);
        }
    }
}
