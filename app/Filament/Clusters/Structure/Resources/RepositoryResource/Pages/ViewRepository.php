<?php

namespace App\Filament\Clusters\Structure\Resources\RepositoryResource\Pages;

use App\Filament\Clusters\Structure\Resources\RepositoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRepository extends ViewRecord
{
    protected static string $resource = RepositoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Назад')
                ->url(RepositoryResource::getUrl())
                ->icon('mdi-arrow-left-thick')
                ->color('info'),
            Actions\EditAction::make()->icon('mdi-pencil'),
        ];
    }
}
