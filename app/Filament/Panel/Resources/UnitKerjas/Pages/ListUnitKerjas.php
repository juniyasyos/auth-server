<?php

namespace App\Filament\Panel\Resources\UnitKerjas\Pages;

use App\Actions\ImportUnitKerjasFromJsonAction;
use App\Filament\Panel\Resources\UnitKerjas\UnitKerjaResource;
use App\Jobs\PushUnitKerjaToClient;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListUnitKerjas extends ListRecords
{
    protected static string $resource = UnitKerjaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Data')
                ->icon('heroicon-m-plus'),

            Action::make('importFromJson')
                ->label('Import Unit Kerja (JSON)')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('success')
                ->schema([
                    \Filament\Forms\Components\FileUpload::make('json_file')
                        ->label('Upload File JSON')
                        ->acceptedFileTypes(['application/json'])
                        ->maxSize(5120) // 5MB
                        ->storeFiles(false)
                        ->required()
                        ->helperText('Format: JSON array berisi data unit kerja. Max 5MB.'),

                    \Filament\Forms\Components\Toggle::make('skip_errors')
                        ->label('Lanjutkan meski ada error')
                        ->default(true)
                        ->helperText('Jika aktif, import akan tetap berjalan meski ada data yang gagal.'),
                ])
                ->action(function (array $data, ImportUnitKerjasFromJsonAction $importAction, Request $request): void {
                    try {
                        \Log::debug('=== Import JSON Unit Kerja Started ===');
                        \Log::debug('Form data keys: ' . implode(', ', array_keys($data)));

                        $jsonContent = null;
                        $sourceFile = null;

                        if (isset($data['json_file'])) {
                            $fileData = $data['json_file'];

                            if (is_object($fileData)) {
                                \Log::debug('json_file is object: ' . get_class($fileData));

                                if (method_exists($fileData, 'getRealPath')) {
                                    $filePath = $fileData->getRealPath();
                                    \Log::debug('Object has getRealPath, path: ' . $filePath);

                                    if ($filePath && file_exists($filePath)) {
                                        $jsonContent = file_get_contents($filePath);
                                        $sourceFile = $filePath;
                                        \Log::debug('Successfully read from getRealPath', ['size' => strlen($jsonContent)]);
                                    }
                                }

                                if (!$jsonContent && method_exists($fileData, '__toString')) {
                                    $filePath = (string) $fileData;
                                    \Log::debug('Object has __toString, path: ' . $filePath);

                                    if ($filePath && file_exists($filePath)) {
                                        $jsonContent = file_get_contents($filePath);
                                        $sourceFile = $filePath;
                                        \Log::debug('Successfully read from __toString', ['size' => strlen($jsonContent)]);
                                    }
                                }
                            } elseif (is_string($fileData)) {
                                \Log::debug('json_file is string path: ' . $fileData);

                                if (file_exists($fileData)) {
                                    $jsonContent = file_get_contents($fileData);
                                    $sourceFile = $fileData;
                                    \Log::debug('Successfully read from string path', ['size' => strlen($jsonContent)]);
                                } else {
                                    $disk = Storage::disk();
                                    if ($disk->exists($fileData)) {
                                        $jsonContent = $disk->get($fileData);
                                        $sourceFile = $fileData;
                                        \Log::debug('Successfully read from disk', ['size' => strlen($jsonContent)]);
                                    }
                                }
                            }
                        }

                        if (!$jsonContent) {
                            \Log::error('Failed to read JSON content for unit kerja import', [
                                'fileDataType' => isset($data['json_file']) ? gettype($data['json_file']) : 'not set',
                                'fileDataClass' => isset($data['json_file']) && is_object($data['json_file']) ? get_class($data['json_file']) : 'N/A',
                                'sourceFile' => $sourceFile,
                            ]);

                            Notification::make()
                                ->title('Gagal membaca file JSON')
                                ->danger()
                                ->send();

                            return;
                        }

                        $unitsData = json_decode($jsonContent, true);

                        if (! is_array($unitsData)) {
                            \Log::error('Invalid JSON format for unit kerja import', ['jsonError' => json_last_error_msg()]);
                            Notification::make()
                                ->title('Format JSON tidak valid')
                                ->body('File harus berisi array JSON unit kerja.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $skipErrors = (bool) ($data['skip_errors'] ?? true);
                        $result = $importAction->execute($unitsData, $skipErrors);

                        Notification::make()
                            ->title('Import unit kerja selesai')
                            ->body(sprintf(
                                'Total: %d, dibuat: %d, diperbarui: %d, gagal: %d',
                                $result['total'] ?? 0,
                                $result['created'] ?? 0,
                                $result['updated'] ?? 0,
                                $result['failed'] ?? 0,
                            ))
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        \Log::error('Import unit kerja failed', [
                            'exception' => get_class($e),
                            'message' => $e->getMessage(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                            'trace' => $e->getTraceAsString(),
                        ]);

                        Notification::make()
                            ->title('Error saat import')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->modalHeading('Import Unit Kerja dari JSON')
                ->modalDescription('Upload file JSON berisi daftar unit kerja. Data akan di-upsert untuk mengurangi duplikasi.')
                ->modalSubmitActionLabel('Import')
                ->modalWidth('2xl'),

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
