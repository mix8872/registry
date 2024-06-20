<?php

namespace App\Filament\Widgets;

use App\Models\Repository;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RepositoriesOverview extends BaseWidget
{
    protected static ?string $heading = 'Последние 10 репозиториев';
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Repository::query()->take(10)->orderBy('updated_at', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()
                    ->description(fn(Repository $r) => $r->comment)
                    ->url(fn(Repository $r): string|null => "/registry/repositories/{$r->id}/edit", true)
                    ->label('Название'),
                Tables\Columns\SelectColumn::make('url')
                    ->url(fn(Repository $r): string => $r->url ?? '', true)
                    ->sortable()->label('Ссылка'),
                Tables\Columns\TextColumn::make('updated_at')->sortable()->dateTime()
                    ->description(fn(Repository $r) => $r->updatedBy->name)->label('Обновлено'),
            ])
            ->paginated(false);
    }
}
