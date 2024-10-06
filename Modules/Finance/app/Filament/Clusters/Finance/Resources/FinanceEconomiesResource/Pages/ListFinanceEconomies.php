<?php

namespace Modules\Finance\Filament\Clusters\Finance\Resources\FinanceEconomiesResource\Pages;

use Modules\Finance\Filament\Clusters\Finance\Resources\FinanceEconomiesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFinanceEconomies extends ListRecords
{
    protected static string $resource = FinanceEconomiesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
