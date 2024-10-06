<?php

namespace Modules\Finance\Filament\Clusters;

use Filament\Clusters\Cluster;
use Nwidart\Modules\Facades\Module;

class Finance extends Cluster
{
    protected static null|string $title = 'Финансы';

    public static function getModuleName(): string
    {
        return 'Finance';
    }

    public static function getModule(): \Nwidart\Modules\Module
    {
        return Module::findOrFail(static::getModuleName());
    }

    public static function getNavigationLabel(): string
    {
        return __('Финансы');
    }

    public static function getNavigationIcon(): ?string
    {
        return 'mdi-cash-multiple';
    }
}
