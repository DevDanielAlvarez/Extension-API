<?php

namespace App\Services\StockDonation;

use App\DTO\StockDonation\CreateStockDonationDTO;
use App\DTO\StockMovement\CreateStockMovementDTO;
use App\Enums\StockDonationStatusEnum;
use App\Enums\StockMovementTypeEnum;
use App\Models\StockDonation;
use App\Services\StockMovement\StockMovementService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockDonationService
{
    public function __construct(
        protected Model $record
    ) {
    }

    public function getRecord(): Model
    {
        return $this->record;
    }

    public static function create(CreateStockDonationDTO $dtoToCreate): static
    {
        return new self(StockDonation::create($dtoToCreate->toArray()));
    }

    public static function find(string $id): static
    {
        return new self(StockDonation::findOrFail($id));
    }

    /**
     * Marks the donation as received: registers an IN movement (item entered the
     * facility) followed by an OUT movement to the patient (handed over directly),
     * so the general stock balance nets to unchanged while both events stay on record.
     *
     * @throws ValidationException
     */
    public static function confirm(string $id, string $adminUserId): static
    {
        $donation = StockDonation::findOrFail($id);

        self::assertPending($donation);

        DB::transaction(function () use ($donation, $adminUserId): void {
            $now = Carbon::now();

            StockMovementService::create(new CreateStockMovementDTO(
                stock_item_id: $donation->stock_item_id,
                type: StockMovementTypeEnum::IN,
                quantity: $donation->quantity,
                patient_id: null,
                user_id: $adminUserId,
                batch: null,
                expiry_date: null,
                notes: "Recebido via doação de {$donation->donor_name}.",
                movement_date: $now,
                stock_donation_id: $donation->id,
            ));

            StockMovementService::create(new CreateStockMovementDTO(
                stock_item_id: $donation->stock_item_id,
                type: StockMovementTypeEnum::OUT,
                quantity: $donation->quantity,
                patient_id: $donation->patient_id,
                user_id: $adminUserId,
                batch: null,
                expiry_date: null,
                notes: "Entregue ao paciente via doação de {$donation->donor_name}.",
                movement_date: $now,
                stock_donation_id: $donation->id,
            ));

            $donation->update([
                'status' => StockDonationStatusEnum::CONFIRMED,
                'reviewed_by_user_id' => $adminUserId,
                'reviewed_at' => $now,
            ]);
        });

        return new self($donation->fresh());
    }

    /**
     * @throws ValidationException
     */
    public static function cancel(string $id, string $adminUserId): static
    {
        $donation = StockDonation::findOrFail($id);

        self::assertPending($donation);

        $donation->update([
            'status' => StockDonationStatusEnum::CANCELLED,
            'reviewed_by_user_id' => $adminUserId,
            'reviewed_at' => Carbon::now(),
        ]);

        return new self($donation);
    }

    protected static function assertPending(StockDonation $donation): void
    {
        if ($donation->status !== StockDonationStatusEnum::PENDING) {
            throw ValidationException::withMessages([
                'status' => 'Esta doação já foi analisada.',
            ]);
        }
    }
}
