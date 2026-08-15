<?php

namespace App\Models;

use App\Enums\StockMovementTypeEnum;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    /** @use HasFactory<\Database\Factories\StockMovementFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'stock_item_id',
        'stock_donation_id',
        'type',
        'quantity',
        'patient_id',
        'user_id',
        'batch',
        'expiry_date',
        'notes',
        'movement_date',
    ];

    protected $casts = [
        'type' => StockMovementTypeEnum::class,
        'quantity' => 'integer',
        'expiry_date' => 'date:Y-m-d',
        'movement_date' => 'datetime',
    ];

    public function stockItem(): BelongsTo
    {
        return $this->belongsTo(StockItem::class);
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(StockDonation::class, 'stock_donation_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
