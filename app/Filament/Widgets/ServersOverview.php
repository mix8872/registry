<?php

namespace App\Filament\Widgets;

use App\Models\Server;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ServersOverview extends BaseWidget
{
    protected static ?string $heading = 'Последние 10 серверов';
    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Server::query()->take(10)->orderBy('updated_at', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()
                    ->description(fn(Server $r) => $r->comment)
                    ->url(fn(Server $r): string|null => "/registry/servers/{$r->id}/edit", true)
                    ->label('Название'),
                Tables\Columns\TextColumn::make('updated_at')->sortable()->dateTime()
                    ->description(fn(Server $r) => $r->updatedBy->name)->label('Обновлено'),
            ])
            ->paginated(false);
    }
}
