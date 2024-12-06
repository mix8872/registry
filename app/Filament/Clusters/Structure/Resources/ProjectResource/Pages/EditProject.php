<?php

namespace App\Filament\Clusters\Structure\Resources\ProjectResource\Pages;

use App\Filament\Clusters\Structure\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Назад')
                ->url(ProjectResource::getUrl())
                ->icon('mdi-arrow-left-thick')
                ->color('info'),
            Actions\DeleteAction::make()
                ->hidden(fn() => !auth()->user()->hasRole('admin'))
                ->icon('mdi-close-thick'),
        ];
    }
}
