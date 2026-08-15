<?php

namespace App\Services\StockMovement;

use App\DTO\StockMovement\CreateStockMovementDTO;
use App\Enums\StockItemCategoryEnum;
use App\Enums\StockMovementTypeEnum;
use App\Models\StockItem;
use App\Models\StockMovement;
use App\Services\StockItem\StockItemService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class StockMovementService
{
    public function __construct(
        protected Model $record
    ) {
    }

    public function getRecord(): Model
    {
        return $this->record;
    }

    public static function create(CreateStockMovementDTO $dtoToCreate): static
    {
        $stockItem = StockItem::findOrFail($dtoToCreate->stock_item_id);

        self::assertPatientRequirement($stockItem, $dtoToCreate);
        self::assertBatchRequirement($stockItem, $dtoToCreate);

        $stockItemService = new StockItemService($stockItem);

        // ADJUSTMENT corrects the balance to the counted value (physical inventory count);
        // the other types apply a signed delta on top of the current balance.
        match ($dtoToCreate->type) {
            StockMovementTypeEnum::IN => $stockItemService->adjustBalance($dtoToCreate->quantity),
            StockMovementTypeEnum::OUT => $stockItemService->adjustBalance(-$dtoToCreate->quantity),
            StockMovementTypeEnum::RETURNED => $stockItemService->adjustBalance($dtoToCreate->quantity),
            StockMovementTypeEnum::ADJUSTMENT => $stockItemService->setBalance($dtoToCreate->quantity),
        };

        return new self(StockMovement::create($dtoToCreate->toArray()));
    }

    public static function find(string $id): static
    {
        $movement = StockMovement::findOrFail($id);

        return new self($movement);
    }

    protected static function assertPatientRequirement(StockItem $stockItem, CreateStockMovementDTO $dto): void
    {
        $requiresPatient = $stockItem->category === StockItemCategoryEnum::RESIDENT_SUPPLY
            && in_array($dto->type, [StockMovementTypeEnum::OUT, StockMovementTypeEnum::RETURNED], true);

        if ($requiresPatient && ! $dto->patient_id) {
            throw ValidationException::withMessages([
                'patient_id' => 'Itens de enxoval do residente exigem um paciente vinculado nesta movimentação.',
            ]);
        }
    }

    protected static function assertBatchRequirement(StockItem $stockItem, CreateStockMovementDTO $dto): void
    {
        if (! $stockItem->requires_batch_control || $dto->type !== StockMovementTypeEnum::IN) {
            return;
        }

        if (! $dto->batch || ! $dto->expiry_date) {
            throw ValidationException::withMessages([
                'batch' => 'Este item exige lote e validade em movimentações de entrada.',
            ]);
        }
    }
}
