<?php

namespace App\Models;

use App\Enums\StockDonationStatusEnum;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockDonation extends Model
{
    /** @use HasFactory<\Database\Factories\StockDonationFactory> */
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'stock_item_id',
        'patient_id',
        'quantity',
        'donor_name',
        'donor_phone',
        'notes',
        'status',
        'reviewed_by_user_id',
        'reviewed_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'status' => StockDonationStatusEnum::class,
        'reviewed_at' => 'datetime',
    ];

    public function stockItem(): BelongsTo
    {
        return $this->belongsTo(StockItem::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }
}
