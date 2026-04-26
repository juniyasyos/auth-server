<?php

namespace App\Filament\Panel\Resources\UnitKerjas\Pages;

use App\Filament\Panel\Resources\UnitKerjas\UnitKerjaResource;
use App\Jobs\PushUnitKerjaToClient;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListUnitKerjas extends ListRecords
{
    protected static string $resource = UnitKerjaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Data')
                ->icon('heroicon-m-plus'),

            Action::make('syncAllUnitKerja')
                ->label('Sinkronisasi Semua Unit Kerja ke Client')
                ->icon('heroicon-m-arrow-path')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (): void {
                    PushUnitKerjaToClient::dispatch([], null);

                    Notification::make()
                        ->title('Sinkronisasi unit kerja dijadwalkan')
                        ->success()
                        ->send();
                }),
        ];
    }
}
