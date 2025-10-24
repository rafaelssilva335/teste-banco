<?php

namespace App\Application\DTO;

use App\Domain\Entities\Transaction;

class TransactionResponseDTO
{
    private string $id;
    private string $sourceAccountId;
    private string $destinationAccountId;
    private float $amount;
    private string $currency;
    private string $type;
    private string $createdAt;

    public function __construct(Transaction $transaction)
    {
        $this->id = $transaction->getId()->getValue();
        $this->sourceAccountId = $transaction->getSourceAccountId();
        $this->destinationAccountId = $transaction->getDestinationAccountId();
        $this->amount = $transaction->getAmount()->getAmount();
        $this->currency = $transaction->getAmount()->getCurrency();
        $this->type = $transaction->getType();
        $this->createdAt = $transaction->getCreatedAt()->format('Y-m-d H:i:s');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'source' => $this->sourceAccountId,
            'destination' => $this->destinationAccountId,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'type' => $this->type,
            'created_at' => $this->createdAt
        ];
    }
}
