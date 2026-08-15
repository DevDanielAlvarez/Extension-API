<?php

namespace App\Filament\Resources\StockDonations\Tables;

use App\Enums\StockDonationStatusEnum;
use App\Models\StockDonation;
use App\Services\StockDonation\StockDonationService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Validation\ValidationException;

class StockDonationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('patient.name')
                    ->label('Residente')
                    ->searchable(),
                TextColumn::make('stockItem.name')
                    ->label('Item')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label('Quantidade')
                    ->numeric(),
                TextColumn::make('donor_name')
                    ->label('Quem trouxe')
                    ->searchable(),
                TextColumn::make('donor_phone')
                    ->label('Telefone')
                    ->placeholder('—'),
                TextColumn::make('notes')
                    ->label('Observações')
                    ->limit(40)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state): string => match ($state instanceof \BackedEnum ? $state->value : $state) {
                        StockDonationStatusEnum::PENDING->value => 'Pendente',
                        StockDonationStatusEnum::CONFIRMED->value => 'Confirmada',
                        StockDonationStatusEnum::CANCELLED->value => 'Cancelada',
                        default => (string) $state,
                    })
                    ->color(fn ($state): string => match ($state instanceof \BackedEnum ? $state->value : $state) {
                        StockDonationStatusEnum::PENDING->value => 'warning',
                        StockDonationStatusEnum::CONFIRMED->value => 'success',
                        StockDonationStatusEnum::CANCELLED->value => 'danger',
                        default => 'gray',
                    })
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Registrado em')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('reviewedBy.name')
                    ->label('Conferido por')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->emptyStateHeading('Nenhuma doação registrada')
            ->emptyStateDescription('Itens registrados na tela pública aparecem aqui para conferência.')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        StockDonationStatusEnum::PENDING->value => 'Pendente',
                        StockDonationStatusEnum::CONFIRMED->value => 'Confirmada',
                        StockDonationStatusEnum::CANCELLED->value => 'Cancelada',
                    ])
                    ->default(StockDonationStatusEnum::PENDING->value),
            ])
            ->recordActions([
                Action::make('confirm')
                    ->label('Confirmar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (StockDonation $record): bool => $record->status === StockDonationStatusEnum::PENDING)
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar recebimento do item')
                    ->modalDescription('Confirma que o item chegou de fato à recepção? Isso vai dar entrada no estoque e registrar a entrega ao residente.')
                    ->action(function (StockDonation $record): void {
                        try {
                            StockDonationService::confirm($record->id, auth()->id());
                        } catch (ValidationException $e) {
                            Notification::make()
                                ->title('Não foi possível confirmar')
                                ->body(collect($e->errors())->flatten()->implode(' '))
                                ->danger()
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title('Doação confirmada')
                            ->success()
                            ->send();
                    }),
                Action::make('cancel')
                    ->label('Cancelar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (StockDonation $record): bool => $record->status === StockDonationStatusEnum::PENDING)
                    ->requiresConfirmation()
                    ->modalHeading('Cancelar registro')
                    ->modalDescription('Use quando o item não chegou ou o registro não procede. Nenhuma movimentação de estoque será feita.')
                    ->action(function (StockDonation $record): void {
                        try {
                            StockDonationService::cancel($record->id, auth()->id());
                        } catch (ValidationException $e) {
                            Notification::make()
                                ->title('Não foi possível cancelar')
                                ->body(collect($e->errors())->flatten()->implode(' '))
                                ->danger()
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title('Registro cancelado')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
