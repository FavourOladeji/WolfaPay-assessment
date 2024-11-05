<?php

namespace App\DataTransferObjects;

use App\Http\Requests\StoreTransactionRequest;

class TransactionDto {

    public function __construct(
        public readonly int $providerId,
        public readonly string $providerTransactionId,
        public readonly int $amount,
    )
    {

    }

    public static function fromRequest(StoreTransactionRequest $request)
    {
        return new self (
            providerId: $request->validated('user_id'),
            providerTransactionId: $request->validated('provider_transaction_id'),
            amount: $request->validated('amount'),
        );
    }


}
