<?php

namespace Modules\Finance\Filament\Clusters\Finance\Resources\FinanceEconomiesResource\Pages;

use Modules\Finance\Filament\Clusters\Finance\Resources\FinanceEconomiesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFinanceEconomies extends EditRecord
{
    protected static string $resource = FinanceEconomiesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Назад')
                ->url(FinanceEconomiesResource::getUrl())
                ->icon('mdi-arrow-left-thick')
                ->color('info'),
            Actions\DeleteAction::make()->icon('mdi-close-thick'),
        ];
    }
}
