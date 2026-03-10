<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'name',
        'project_date',
        'note',
        'status',
        'opened_by_user_id',
        'closed_by_user_id',
        'closed_at',
    ];

    protected $casts = [
        'project_date' => 'date',
        'closed_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function opener(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by_user_id');
    }

    public function closer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by_user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CustomerProjectItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(CustomerProjectPayment::class);
    }

    public function getTotalAttribute(): float
    {
        return (float) $this->items()->sum('subtotal');
    }

    public function getPaidAttribute(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function getBalanceAttribute(): float
    {
        return $this->total - $this->paid;
    }
}