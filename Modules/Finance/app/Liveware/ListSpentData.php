<?php

namespace Modules\Finance\Liveware;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Modules\Finance\Models\FinanceSpentFact;

class ListSpentData extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public string|Model $model;
    private bool $disabled = false;


    public function mount($model): void
    {
        $this->model = is_string($model) ? new $model() : $model;
        $this->disabled = !$this->model->id;
    }

    public function table(Table $table): Table
    {
        return $table
            ->relationship(fn(): HasMany => $this->model->spents()) //показывать только связанные проекты
            ->recordTitleAttribute('project.name')
            ->searchable(false)
            ->heading('Финансы')
            ->columns([
                Tables\Columns\TextColumn::make('resource.name')->label('Ресурс'),
                Tables\Columns\TextColumn::make('rate_in')->money('RUB')->label('Цена внутренняя'),
                Tables\Columns\TextColumn::make('rate_out')->money('RUB')->label('Цена внешняя'),
                Tables\Columns\TextColumn::make('sold_count')->label('Часов продано'),
                Tables\Columns\TextColumn::make('spent_count')->time('H:i')->label('Часов потрачено'),
                Tables\Columns\TextColumn::make('relation')->numeric()->suffix('%')->label('Соотношение'),
                Tables\Columns\TextColumn::make('price_in')->money('RUB')->label('Стоимость внутренняя'),
                Tables\Columns\TextColumn::make('price_out')->money('RUB  ')->label('Стоимость внешняя'),
                Tables\Columns\TextColumn::make('performance')->label('Эффективность'),
                Tables\Columns\TextColumn::make('profit')->money('RUB')->label('Доход'),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
