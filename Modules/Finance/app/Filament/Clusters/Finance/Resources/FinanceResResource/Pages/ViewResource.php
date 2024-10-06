<?php

namespace Modules\Finance\Filament\Clusters\Finance\Resources\FinanceResResource\Pages;

use Modules\Finance\Filament\Clusters\Finance\Resources\FinanceResResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewResource extends ViewRecord
{
    protected static string $resource = FinanceResResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
