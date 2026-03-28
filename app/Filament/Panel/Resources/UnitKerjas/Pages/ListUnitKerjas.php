<?php

namespace App\Filament\Panel\Resources\UnitKerjas\Pages;

use App\Filament\Panel\Resources\UnitKerjas\UnitKerjaResource;
use Filament\Actions;
use Juniyasyos\ManageUnitKerja\Filament\Resources\UnitKerjaResource\Pages\ListUnitKerja;

class ListUnitKerjas extends ListUnitKerja
{
    protected static string $resource = UnitKerjaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Data')
                ->icon('heroicon-m-plus'),
        ];
    }
}
