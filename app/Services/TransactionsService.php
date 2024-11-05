<?php

namespace App\Services;

use App\DataTransferObjects\TransactionDto;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransactionsService
{
    public function createTransaction(TransactionDto $transactionDto)
    {
        return DB::transaction(function () use ($transactionDto) {
            $transaction = Transaction::create([
                'provider_id' => $transactionDto->providerId,
                'provider_transaction_id' => $transactionDto->providerTransactionId,
                'amount' => $transactionDto->amount,
                'reference_number' => Transaction::generateReferenceNumber()
            ]);

            sleep(3); // post-processing
            return $transaction;
        });

    }
}
