<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerProjectItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_project_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
        'added_by_user_id',
        'added_at',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'added_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(CustomerProject::class, 'customer_project_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by_user_id');
    }
}