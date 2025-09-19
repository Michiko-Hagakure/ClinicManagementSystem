<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_id',
        'patient_name',
        'patient_id',
        'services',
        'service_total',
        'medicine_total',
        'total_amount',
        'payment_method',
        'amount_paid',
        'change_amount',
        'status',
        'cashier',
        'receipt_number',
        'notes'
    ];

    protected $casts = [
        'services' => 'array',
        'service_total' => 'decimal:2',
        'medicine_total' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];
}
