<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerProjectPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_project_id',
        'amount',
        'payment_date',
        'note',
        'received_by_user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(CustomerProject::class, 'customer_project_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by_user_id');
    }
}