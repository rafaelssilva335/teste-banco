<?php

namespace App\Domain\Services;

use App\Domain\Entities\Transaction;
use App\Domain\Repositories\TransactionRepository;
use App\Domain\Repositories\AccountRepository;
use App\Domain\ValueObjects\Money;
use App\Domain\ValueObjects\TransactionId;

class TransactionService
{
    private TransactionRepository $transactionRepository;
    private AccountRepository $accountRepository;

    public function __construct(
        TransactionRepository $transactionRepository,
        AccountRepository $accountRepository
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->accountRepository = $accountRepository;
    }

    public function createDeposit(string $accountId, float $amount, string $currency = 'USD'): Transaction
    {
        $money = new Money($amount, $currency);
        $transaction = Transaction::createDeposit($accountId, $money);
        
        $account = $this->accountRepository->findById($accountId);
        if (!$account) {
            throw new \DomainException('Account not found');
        }
        
        $account->deposit($amount);
        $this->accountRepository->save($account);
        $this->transactionRepository->save($transaction);
        
        return $transaction;
    }

    public function createWithdrawal(string $accountId, float $amount, string $currency = 'USD'): Transaction
    {
        $money = new Money($amount, $currency);
        $transaction = Transaction::createWithdrawal($accountId, $money);
        
        $account = $this->accountRepository->findById($accountId);
        if (!$account) {
            throw new \DomainException('Account not found');
        }
        
        $account->withdraw($amount);
        $this->accountRepository->save($account);
        $this->transactionRepository->save($transaction);
        
        return $transaction;
    }

    public function createTransfer(string $sourceAccountId, string $destinationAccountId, float $amount, string $currency = 'USD'): Transaction
    {
        $money = new Money($amount, $currency);
        $transaction = Transaction::createTransfer($sourceAccountId, $destinationAccountId, $money);
        
        $sourceAccount = $this->accountRepository->findById($sourceAccountId);
        if (!$sourceAccount) {
            throw new \DomainException('Source account not found');
        }
        
        $destinationAccount = $this->accountRepository->findById($destinationAccountId);
        if (!$destinationAccount) {
            throw new \DomainException('Destination account not found');
        }
        
        $sourceAccount->transfer($amount, $destinationAccount);
        $this->accountRepository->save($sourceAccount);
        $this->accountRepository->save($destinationAccount);
        $this->transactionRepository->save($transaction);
        
        return $transaction;
    }

    public function getTransactionById(TransactionId $id): ?Transaction
    {
        return $this->transactionRepository->findById($id);
    }

    public function getTransactionsByAccountId(string $accountId): array
    {
        return $this->transactionRepository->findByAccountId($accountId);
    }
}
