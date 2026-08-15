<?php

namespace App\Models;

use App\Enums\StockItemCategoryEnum;
use App\Enums\StockItemUnitEnum;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockItem extends Model
{
    /** @use HasFactory<\Database\Factories\StockItemFactory> */
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'name',
        'category',
        'unit',
        'current_quantity',
        'minimum_quantity',
        'requires_batch_control',
        'additional_information',
    ];

    protected $casts = [
        'category' => StockItemCategoryEnum::class,
        'unit' => StockItemUnitEnum::class,
        'current_quantity' => 'integer',
        'minimum_quantity' => 'integer',
        'requires_batch_control' => 'boolean',
    ];

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function isLowStock(): bool
    {
        return $this->minimum_quantity !== null && $this->current_quantity <= $this->minimum_quantity;
    }
}
