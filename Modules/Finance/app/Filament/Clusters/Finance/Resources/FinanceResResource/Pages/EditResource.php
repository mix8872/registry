<?php

namespace Modules\Finance\Filament\Clusters\Finance\Resources\FinanceResResource\Pages;

use Modules\Finance\Filament\Clusters\Finance\Resources\FinanceResResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditResource extends EditRecord
{
    protected static string $resource = FinanceResResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Назад')
                ->url(FinanceResResource::getUrl())
                ->icon('mdi-arrow-left-thick')
                ->color('info'),
            Actions\DeleteAction::make()->icon('mdi-close-thick'),
        ];
    }
}
