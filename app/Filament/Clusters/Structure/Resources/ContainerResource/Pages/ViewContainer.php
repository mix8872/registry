<?php

namespace App\Filament\Clusters\Structure\Resources\ContainerResource\Pages;

use App\Filament\Clusters\Structure\Resources\ContainerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewContainer extends ViewRecord
{
    protected static string $resource = ContainerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
