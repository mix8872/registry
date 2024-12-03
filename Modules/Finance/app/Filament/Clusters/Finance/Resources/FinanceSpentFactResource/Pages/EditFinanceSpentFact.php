<?php

namespace Modules\Finance\Filament\Clusters\Finance\Resources\FinanceSpentFactResource\Pages;

use Modules\Finance\Filament\Clusters\Finance\Resources\FinanceSpentFactResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFinanceSpentFact extends EditRecord
{
    protected static string $resource = FinanceSpentFactResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return empty($parameters['record']->crm_id);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Назад')
                ->url(FinanceSpentFactResource::getUrl())
                ->icon('mdi-arrow-left-thick')
                ->color('info'),
            Actions\DeleteAction::make()->icon('mdi-close-thick'),
        ];
    }
}
