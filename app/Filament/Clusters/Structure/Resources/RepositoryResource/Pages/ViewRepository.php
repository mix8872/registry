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
            Actions\EditAction::make(),
        ];
    }
}
