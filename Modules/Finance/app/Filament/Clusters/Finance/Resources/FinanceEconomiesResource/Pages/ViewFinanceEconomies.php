<?php

namespace Modules\Finance\Filament\Clusters\Finance\Resources\FinanceEconomiesResource\Pages;

use Modules\Finance\Filament\Clusters\Finance\Resources\FinanceEconomiesResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFinanceEconomies extends ViewRecord
{
    protected static string $resource = FinanceEconomiesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
