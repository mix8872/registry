<?php

namespace App\Filament\Clusters\Structure\Resources\StackResource\Pages;

use App\Filament\Clusters\Structure\Resources\StackResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStack extends EditRecord
{
    protected static string $resource = StackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
