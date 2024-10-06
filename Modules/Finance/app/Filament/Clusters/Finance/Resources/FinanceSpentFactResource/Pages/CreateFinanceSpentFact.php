<?php

namespace Modules\Finance\Filament\Clusters\Finance\Resources\FinanceSpentFactResource\Pages;

use Modules\Finance\Filament\Clusters\Finance\Resources\FinanceSpentFactResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFinanceSpentFact extends CreateRecord
{
    protected static string $resource = FinanceSpentFactResource::class;
}
