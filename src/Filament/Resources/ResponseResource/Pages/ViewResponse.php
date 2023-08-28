<?php

namespace LaraZeus\Bolt\Filament\Resources\ResponseResource\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;
use LaraZeus\Bolt\BoltPlugin;
use LaraZeus\Bolt\Filament\Resources\ResponseResource;
use LaraZeus\Bolt\Models\Response;

/**
 * @property Response $record.
 */
class ViewResponse extends ViewRecord
{
    protected static string $resource = ResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('set-status')
                ->visible(function (): bool {
                    return $this->record->form->extensions === null;
                })
                ->label(__('Set Status'))
                ->icon('heroicon-o-tag')
                ->action(function (array $data): void {
                    $this->record->status = $data['status'];
                    $this->record->notes = $data['notes'];
                    $this->record->save();
                })
                ->form([
                    Select::make('status')
                        ->label(__('status'))
                        ->default(fn (Response $record) => $record->status)
                        ->options(BoltPlugin::getModel('FormsStatus')::query()->pluck('label', 'key'))
                        ->required(),
                    Textarea::make('notes')
                        ->default(fn (Response $record) => $record->notes)
                        ->label(__('Notes')),
                ]),
        ];
    }
}
