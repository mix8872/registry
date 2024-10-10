<?php

namespace App\Filament\Clusters\Structure\Resources\CustomerResource\Pages;

use App\Filament\Clusters\Structure\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Назад')
                ->url(CustomerResource::getUrl())
                ->icon('mdi-arrow-left-thick')
                ->color('info'),
            Actions\DeleteAction::make()->icon('mdi-close-thick'),
        ];
    }
}
