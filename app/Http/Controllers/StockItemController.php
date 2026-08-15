<?php

namespace App\Http\Controllers;

use App\DTO\StockItem\CreateStockItemDTO;
use App\DTO\StockItem\UpdateStockItemDTO;
use App\DTO\StockMovement\CreateStockMovementDTO;
use App\Enums\StockItemCategoryEnum;
use App\Enums\StockItemUnitEnum;
use App\Enums\StockMovementTypeEnum;
use App\Http\Requests\StockItem\CreateStockItemFormRequest;
use App\Http\Requests\StockItem\UpdateStockItemFormRequest;
use App\Http\Requests\StockMovement\CreateStockMovementFormRequest;
use App\Http\Resources\StockItemResource;
use App\Http\Resources\StockMovementResource;
use App\Models\StockItem;
use App\Services\StockItem\StockItemService;
use App\Services\StockMovement\StockMovementService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StockItemController extends Controller
{
    public function index()
    {
        return StockItemResource::collection(StockItem::paginate(10));
    }

    public function trashed()
    {
        return StockItemResource::collection(StockItem::onlyTrashed()->paginate(10));
    }

    public function lowStock()
    {
        return StockItemResource::collection(
            StockItem::whereNotNull('minimum_quantity')
                ->whereColumn('current_quantity', '<=', 'minimum_quantity')
                ->paginate(10)
        );
    }

    public function store(CreateStockItemFormRequest $request)
    {
        $validatedData = $request->validated();

        $result = DB::transaction(function () use ($validatedData) {
            $dtoToCreate = new CreateStockItemDTO(
                name: $validatedData['name'],
                category: StockItemCategoryEnum::from($validatedData['category']),
                unit: StockItemUnitEnum::from($validatedData['unit']),
                current_quantity: $validatedData['current_quantity'] ?? 0,
                minimum_quantity: $validatedData['minimum_quantity'] ?? null,
                requires_batch_control: $validatedData['requires_batch_control'] ?? false,
                additional_information: $validatedData['additional_information'] ?? null,
            );

            $stockItemService = StockItemService::create($dtoToCreate);

            return new StockItemResource($stockItemService->getRecord());
        });

        return $result->response()->setStatusCode(201);
    }

    public function show(string $stockItem)
    {
        $stockItemService = StockItemService::find($stockItem);

        return StockItemResource::make($stockItemService->getRecord());
    }

    public function update(UpdateStockItemFormRequest $request, string $stockItem)
    {
        $validatedData = $request->validated();

        $result = DB::transaction(function () use ($validatedData, $stockItem) {
            $dtoToUpdate = new UpdateStockItemDTO(
                id: $stockItem,
                name: $validatedData['name'],
                category: StockItemCategoryEnum::from($validatedData['category']),
                unit: StockItemUnitEnum::from($validatedData['unit']),
                minimum_quantity: $validatedData['minimum_quantity'] ?? null,
                requires_batch_control: $validatedData['requires_batch_control'] ?? false,
                additional_information: $validatedData['additional_information'] ?? null,
            );

            $stockItemService = StockItemService::find($stockItem);
            $stockItemService->update($dtoToUpdate);

            return StockItemResource::make($stockItemService->getRecord());
        });

        return $result;
    }

    public function destroy(string $stockItem)
    {
        $stockItemService = StockItemService::find($stockItem);
        $stockItemService->delete();

        return response()->noContent();
    }

    public function restore(string $stockItem)
    {
        $record = StockItem::onlyTrashed()->findOrFail($stockItem);
        $record->restore();

        return StockItemResource::make($record->fresh());
    }

    public function forceDelete(string $stockItem)
    {
        $record = StockItem::withTrashed()->findOrFail($stockItem);
        $record->forceDelete();

        return response()->noContent();
    }

    public function movements(string $stockItem)
    {
        $stockItemService = StockItemService::find($stockItem);

        return StockMovementResource::collection(
            $stockItemService->getRecord()->movements()->latest('movement_date')->paginate(10)
        );
    }

    public function storeMovement(CreateStockMovementFormRequest $request, string $stockItem)
    {
        $validatedData = $request->validated();

        $result = DB::transaction(function () use ($validatedData, $stockItem) {
            $dtoToCreate = new CreateStockMovementDTO(
                stock_item_id: $stockItem,
                type: StockMovementTypeEnum::from($validatedData['type']),
                quantity: $validatedData['quantity'],
                patient_id: $validatedData['patient_id'] ?? null,
                user_id: auth()->id(),
                batch: $validatedData['batch'] ?? null,
                expiry_date: isset($validatedData['expiry_date']) ? Carbon::parse($validatedData['expiry_date']) : null,
                notes: $validatedData['notes'] ?? null,
                movement_date: isset($validatedData['movement_date']) ? Carbon::parse($validatedData['movement_date']) : Carbon::now(),
            );

            $movementService = StockMovementService::create($dtoToCreate);

            return new StockMovementResource($movementService->getRecord());
        });

        return $result->response()->setStatusCode(201);
    }
}
