<?php

namespace App\Services\StockItem;

use App\DTO\StockItem\CreateStockItemDTO;
use App\DTO\StockItem\UpdateStockItemDTO;
use App\Models\StockItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class StockItemService
{
    public function __construct(
        protected Model $record
    ) {
    }

    public function getRecord(): Model
    {
        return $this->record;
    }

    public static function create(CreateStockItemDTO $dtoToCreate): static
    {
        return new self(StockItem::create($dtoToCreate->toArray()));
    }

    public static function find(string $id): static
    {
        $stockItem = StockItem::findOrFail($id);

        return new self($stockItem);
    }

    public function update(UpdateStockItemDTO $dtoToUpdate): static
    {
        $this->record->update($dtoToUpdate->toArray());

        return $this;
    }

    public function delete(): void
    {
        $this->record->delete();
    }

    public function adjustBalance(int $delta): static
    {
        return $this->setBalance($this->record->current_quantity + $delta);
    }

    public function setBalance(int $newQuantity): static
    {
        if ($newQuantity < 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Saldo insuficiente para esta movimentação.',
            ]);
        }

        $this->record->update(['current_quantity' => $newQuantity]);

        return $this;
    }
}
