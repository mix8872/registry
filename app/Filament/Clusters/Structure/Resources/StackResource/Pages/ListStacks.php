<?php

namespace App\Filament\Clusters\Structure\Resources\StackResource\Pages;

use App\Filament\Clusters\Structure\Resources\StackResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;

class ListStacks extends ListRecords
{
    protected static string $resource = StackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->slideOver()->modalWidth(MaxWidth::SevenExtraLarge),
        ];
    }
}
