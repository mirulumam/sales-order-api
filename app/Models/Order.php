<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public const STATUS_DRAFT     = 'Draft';
    public const STATUS_SUBMITTED = 'Submitted';
    public const STATUS_CANCELLED = 'Cancelled';
    public const STATUS_MAP = [
        1 => self::STATUS_DRAFT,
        2 => self::STATUS_SUBMITTED,
        3 => self::STATUS_CANCELLED,
    ];

    protected $fillable = [
        'order_no',
        'customer_id',
        'status',
        'total_amount',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isSubmitted(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }
}
