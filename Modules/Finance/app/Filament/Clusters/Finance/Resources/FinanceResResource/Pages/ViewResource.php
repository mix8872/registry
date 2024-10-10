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
            Actions\Action::make('Назад')
                ->url(FinanceResResource::getUrl())
                ->icon('mdi-arrow-left-thick')
                ->color('info'),
            Actions\EditAction::make()->icon('mdi-pencil'),
        ];
    }
}
