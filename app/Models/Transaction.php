<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'provider_id',
        'provider_transaction_id',
        'amount',
        'reference_number',
    ];

    public static function generateReferenceNumber() {
        $timestamp = now()->format('YmdHis');
        $randomString = Str::random(6);
        return $randomString . $timestamp;
    }
}
