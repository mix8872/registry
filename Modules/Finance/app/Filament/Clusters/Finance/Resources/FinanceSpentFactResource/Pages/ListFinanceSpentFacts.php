<?php

namespace Modules\Finance\Filament\Clusters\Finance\Resources\FinanceSpentFactResource\Pages;

use Modules\Finance\Filament\Clusters\Finance\Resources\FinanceSpentFactResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFinanceSpentFacts extends ListRecords
{
    protected static string $resource = FinanceSpentFactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
