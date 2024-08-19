<?php

namespace App\Livewire;

use App\Models\Project;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class ListProjects extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public Model $model;
    public string $operation;

    public function mount($model, string $operation): void
    {
        $this->model = $model;
        $this->operation = $operation;
    }

    public function table(Table $table): Table
    {
        return $table
            ->relationship(fn(): BelongsToMany => $this->model->projects())
            ->recordTitleAttribute('name')
            ->searchable(false)
            ->heading('Проекты')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()->sortable()
                    ->description(fn(Model $r) => $r->comment)
                    ->url(fn(Project $r) => $r ? "/registry/projects/{$r->id}" : null, true)
                    ->label('Название'),
                Tables\Columns\SelectColumn::make('status')->selectablePlaceholder(false)
                    ->disabled()
                    ->options(Project::$statuses)->sortable()
                    ->label('Статус'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Project::$statuses)
                    ->label('Статус'),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                ->hidden(fn () => $this->operation == 'view' || !auth()->user()->can('update_stack')),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->multiple()
                    ->hidden(fn() => $this->operation == 'view' || !auth()->user()->can('update_stack')),
            ]);
    }

    public function render(): View
    {
        return view('livewire.list-projects');
    }
}
