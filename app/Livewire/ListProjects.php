<?php

namespace App\Livewire;

use App\Filament\Clusters\Structure\Resources\ProjectResource;
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

    public string|Model $model;
    public string $operation;
    private bool $disabled = false;


    public function mount($model, string $operation): void
    {
        $this->model = is_string($model) ? new $model() : $model;
        $this->operation = $operation;
        $this->disabled = !$this->model->id;
    }

    public function table(Table $table): Table
    {
        return $table
            ->relationship(fn(): BelongsToMany => $this->model->projects()) //показывать только связанные проекты
            ->recordTitleAttribute('name')
            ->searchable(false)
            ->heading('Проекты')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()->sortable()
                    ->description(fn(Model $r) => $r->comment)
                    ->url(fn(Project $r) => $r ? ProjectResource::getUrl('view', ['record' => $r->id]) : null, true)
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
        return view('livewire.table');
    }
}
