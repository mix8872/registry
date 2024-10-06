<?php

namespace Modules\Finance\Filament\Clusters\Finance\Resources\FinanceResResource\Pages;

use Filament\Support\Enums\MaxWidth;
use Modules\Finance\Filament\Clusters\Finance\Resources\FinanceResResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListResources extends ListRecords
{
    protected static string $resource = FinanceResResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->slideOver()->modalWidth(MaxWidth::SevenExtraLarge),
        ];
    }
}
