<?php

namespace Modules\Finance\Filament\Clusters\Finance\Resources\FinanceEconomiesResource\Pages;

use Illuminate\Database\Eloquent\Model;
use Modules\Finance\Filament\Clusters\Finance\Actions\RecalcEconomyAction;
use Modules\Finance\Filament\Clusters\Finance\Resources\FinanceEconomiesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Finance\Models\FinanceEconomy;

class EditFinanceEconomies extends EditRecord
{
    protected static string $resource = FinanceEconomiesResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return in_array(auth()->user()->id, [
                $parameters['record']->created_by,
                $parameters['record']->project->created_by
            ]) || auth()->user()->hasRole('admins');
    }

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

    protected function getFormActions(): array
    {
        return array_merge(parent::getFormActions(), [
            RecalcEconomyAction::make()
        ]);
    }
}
