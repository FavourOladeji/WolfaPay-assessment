<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\TransactionDto;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\TransactionsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    public function __construct(protected TransactionsService $transactionsService)
    {

    }


    /**
     * This function returns a paginated response of all transactions
     * in the system
     * @return
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'provider_id' => ['nullable'],
            'sort_column' => ['nullable', Rule::in('amount', 'created_at')],
            'sort_direction' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => ['nullable', 'integer']
        ]);

        $providerId = $validated['provider_id'] ?? null;
        $sortColumn = $validated['sort_column'] ?? null;
        $sortDirection = $validated['sort_direction'] ?? 'desc';
        $perPage = $validated['per_page'] ?? 10;

        $transactions = Transaction::query()
            ->with('provider:id,name')
            ->when($providerId, function (Builder $query) use ($providerId) {
                return $query->where('provider_id', $providerId);
            })
            ->when($sortColumn, function (Builder $query) use ($sortColumn, $sortDirection) {
                return $query->orderBy($sortColumn, $sortDirection);
            })
            ->paginate($perPage)->withQueryString();

        return TransactionResource::collection($transactions);

    }



    public function store(StoreTransactionRequest $request)
    {
        $transactionDto = TransactionDto::fromRequest($request);
        $transaction = $this->transactionsService->createTransaction($transactionDto);
        return $this->successfulResponse(message: 'Transaction created Successfully', data: new TransactionResource($transaction), status: 201);

    }

    public function show($referenceNumber, Request $request)
    {
        $transaction = Transaction::where('reference_number', $referenceNumber)->first();
        if (!$transaction)
        {
            throw new ModelNotFoundException("Transaction with reference number: {$referenceNumber} not found");
        }
        return $this->successfulResponse(data: new TransactionResource($transaction));
    }
}
