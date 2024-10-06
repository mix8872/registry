<?php

namespace App\Filament\Clusters\Structure\Resources\CustomerResource\Pages;

use App\Filament\Clusters\Structure\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;
}
