<?php

use App\Enums\DocumentTypeEnum;
use App\Enums\StockDonationStatusEnum;
use App\Enums\StockItemCategoryEnum;
use App\Enums\StockMovementTypeEnum;
use App\Models\Patient;
use App\Models\StockDonation;
use App\Models\StockItem;
use App\Models\User;
use App\Services\StockDonation\StockDonationService;
use Illuminate\Validation\ValidationException;

describe('Public donation form (GET/POST /doacoes)', function () {
    it('renders the public donation form', function () {
        StockItem::factory()->create(['name' => 'Travesseiro']);

        $response = $this->get(route('stock-donations.create'));

        $response->assertStatus(200)->assertSee('Travesseiro');
    });

    it('registers a pending donation for an existing patient CPF', function () {
        $patient = Patient::factory()->create([
            'document_type' => DocumentTypeEnum::CPF,
            'document_number' => '12345678900',
        ]);
        $stockItem = StockItem::factory()->create([
            'category' => StockItemCategoryEnum::RESIDENT_SUPPLY,
            'requires_batch_control' => false,
        ]);

        $response = $this->post(route('stock-donations.store'), [
            'patient_document_number' => '123.456.789-00',
            'stock_item_id' => $stockItem->id,
            'quantity' => 2,
            'donor_name' => 'Maria da Silva',
            'donor_phone' => '11999998888',
            'notes' => 'Travesseiro novo, ainda lacrado.',
        ]);

        $response->assertRedirect(route('stock-donations.create'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('stock_donations', [
            'patient_id' => $patient->id,
            'stock_item_id' => $stockItem->id,
            'quantity' => 2,
            'donor_name' => 'Maria da Silva',
            'status' => StockDonationStatusEnum::PENDING->value,
        ]);
    });

    it('rejects a CPF that does not match any patient', function () {
        $stockItem = StockItem::factory()->create();

        $response = $this->post(route('stock-donations.store'), [
            'patient_document_number' => '00000000000',
            'stock_item_id' => $stockItem->id,
            'quantity' => 1,
            'donor_name' => 'João',
        ]);

        $response->assertSessionHasErrors('patient_document_number');
        $this->assertDatabaseCount('stock_donations', 0);
    });

    it('rejects an invalid CPF format', function () {
        $stockItem = StockItem::factory()->create();

        $response = $this->post(route('stock-donations.store'), [
            'patient_document_number' => '123',
            'stock_item_id' => $stockItem->id,
            'quantity' => 1,
            'donor_name' => 'João',
        ]);

        $response->assertSessionHasErrors('patient_document_number');
        $this->assertDatabaseCount('stock_donations', 0);
    });
});

describe('StockDonationService::confirm', function () {
    it('creates IN and OUT movements linked to the donation and nets the balance to unchanged', function () {
        $admin = User::factory()->create();
        $stockItem = StockItem::factory()->create([
            'category' => StockItemCategoryEnum::RESIDENT_SUPPLY,
            'requires_batch_control' => false,
            'current_quantity' => 5,
        ]);
        $donation = StockDonation::factory()->create([
            'stock_item_id' => $stockItem->id,
            'quantity' => 2,
            'status' => StockDonationStatusEnum::PENDING,
        ]);

        StockDonationService::confirm($donation->id, $admin->id);

        $donation->refresh();
        expect($donation->status)->toBe(StockDonationStatusEnum::CONFIRMED);
        expect($donation->reviewed_by_user_id)->toBe($admin->id);
        expect($donation->reviewed_at)->not->toBeNull();

        $this->assertDatabaseHas('stock_items', ['id' => $stockItem->id, 'current_quantity' => 5]);

        $this->assertDatabaseHas('stock_movements', [
            'stock_donation_id' => $donation->id,
            'type' => StockMovementTypeEnum::IN->value,
            'quantity' => 2,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'stock_donation_id' => $donation->id,
            'type' => StockMovementTypeEnum::OUT->value,
            'quantity' => 2,
            'patient_id' => $donation->patient_id,
        ]);
    });

    it('refuses to confirm a donation that was already reviewed', function () {
        $admin = User::factory()->create();
        $donation = StockDonation::factory()->create(['status' => StockDonationStatusEnum::CONFIRMED]);

        expect(fn () => StockDonationService::confirm($donation->id, $admin->id))
            ->toThrow(ValidationException::class);
    });
});

describe('StockDonationService::cancel', function () {
    it('marks the donation as cancelled without creating any movement', function () {
        $admin = User::factory()->create();
        $donation = StockDonation::factory()->create(['status' => StockDonationStatusEnum::PENDING]);

        StockDonationService::cancel($donation->id, $admin->id);

        $donation->refresh();
        expect($donation->status)->toBe(StockDonationStatusEnum::CANCELLED);
        expect($donation->reviewed_by_user_id)->toBe($admin->id);
        $this->assertDatabaseCount('stock_movements', 0);
    });

    it('refuses to cancel a donation that was already reviewed', function () {
        $admin = User::factory()->create();
        $donation = StockDonation::factory()->create(['status' => StockDonationStatusEnum::CANCELLED]);

        expect(fn () => StockDonationService::cancel($donation->id, $admin->id))
            ->toThrow(ValidationException::class);
    });
});
