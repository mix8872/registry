<?php

namespace App\Livewire;

use App\Models\Project;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class AboutProject extends Component implements HasForms, HasInfolists
{
    use InteractsWithInfolists;
    use InteractsWithForms;

//    protected $listeners = ['refreshComponent' => '$refresh'];

    public ?Model $model;

    public function mount($model): void
    {
        $this->model = $model;
    }

    public function render()
    {
        return view('livewire.infolist');
    }

    public function infolist(Infolist $infolist): Infolist|null
    {
        if (!$this->model) {
            return null;
        }
        return $infolist->record($this->model)
            ->schema([
                Components\TextEntry::make('client')
                    ->default('Не задано')
                    ->label('Клиент'),
                Components\TextEntry::make('customer.name')
                    ->default('Не задано')
                    ->label('Заказчик'),
                Components\TextEntry::make('legal_customer')
                    ->default('Не задано')
                    ->label('Юрлицо заказчика'),
                Components\TextEntry::make('legal')
                    ->default('Не задано')
                    ->label('Юрлицо внутреннее'),
                Components\TextEntry::make('contract_date')
                    ->default('Не задано')
                    ->label('Дата заключения'),
                Components\TextEntry::make('contract_close_date')
                    ->default('Не задано')
                    ->label('Дата сдачи'),
                Components\TextEntry::make('payment')
                    ->default('Не задано')
                    ->label('Тип оплаты'),
                Components\TextEntry::make('period')
                    ->default('Не задано')
                    ->label('Периодичность оплаты'),
                Components\TextEntry::make('workType.name')
                    ->default('Не задано')
                    ->label('Тип работы'),
                Components\TextEntry::make('contract')
                    ->default('Не задано')
                    ->label('Договор'),
                Components\TextEntry::make('cost')
                    ->default('Не задано')
                    ->numeric()
                    ->label('Стоимость')
            ])
            ->columns(4);
    }
}
