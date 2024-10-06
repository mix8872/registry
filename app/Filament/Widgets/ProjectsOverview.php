<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ProjectsOverview extends BaseWidget
{
    protected static ?string $heading = 'Последние 10 проектов';
    protected static ?int $sort = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Project::query()->take(10)->orderBy('updated_at', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()
                    ->description(fn(Project $r) => $r->comment)
                    ->url(fn(Project $r): string|null => "/registry/structure/projects/{$r->id}", true)
                    ->label('Название'),
                Tables\Columns\SelectColumn::make('status')->selectablePlaceholder(false)
                    ->options(Project::$statuses)->sortable()->disabled(true)->label('Статус'),
                Tables\Columns\TextColumn::make('updated_at')->sortable()->dateTime()
                    ->description(fn(Project $r) => $r->updatedBy->name)->label('Обновлено'),
            ])
            ->paginated(false)
            ->searchable(false)
            ->poll('5s');
    }
}
