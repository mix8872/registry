<?php

namespace App\Filament\Widgets;

use App\Filament\Clusters\Structure\Resources\ContainerResource;
use App\Models\Container;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ContainersOverview extends BaseWidget
{
    protected static ?string $heading = 'Последние 10 контейнеров';
    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Container::query()->take(10)->orderBy('updated_at', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()
                    ->description(fn(Container $r) => $r->comment)
                    ->url(fn(Container $r): string|null => ContainerResource::getUrl('view', ['record' => $r->id]), true)
                    ->label('Название'),
                Tables\Columns\TextColumn::make('updated_at')->sortable()->dateTime()
                    ->description(fn(Container $r) => $r->updatedBy->name)->label('Обновлено'),
            ])
            ->paginated(false)
            ->searchable(false)
            ->poll('5s');
    }
}
