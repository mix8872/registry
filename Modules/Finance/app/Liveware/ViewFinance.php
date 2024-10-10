<?php

namespace Modules\Finance\Liveware;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class ViewFinance extends Component implements HasForms, HasInfolists
{
    use InteractsWithInfolists;
    use InteractsWithForms;

    public Model $model;

    public function mount($model): void
    {
        $this->model = is_string($model) ? new $model() : $model;
    }

    public function render()
    {
        return view('livewire.infolist');
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->record($this->model)
            ->schema([
                Components\TextEntry::make('project.name')->label('Проект')
            ]);
    }
}
