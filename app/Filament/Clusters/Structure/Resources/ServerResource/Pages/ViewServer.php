<?php

namespace App\Filament\Clusters\Structure\Resources\ServerResource\Pages;

use App\Filament\Clusters\Structure\Resources\ServerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewServer extends ViewRecord
{
    protected static string $resource = ServerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Назад')
                ->url(ServerResource::getUrl())
                ->icon('mdi-arrow-left-thick')
                ->color('info'),
            Actions\EditAction::make()->icon('mdi-pencil'),
        ];
    }
}
