<?php

namespace App\Filament\Resources\Assets\Widgets;

use App\Filament\Pages\ScanAsset;
use App\Filament\Resources\Assets\AssetResource;
use Filament\Widgets\Widget;

class QuickActions extends Widget
{
    protected string $view = 'filament.widgets.quick-actions';

    protected int | string | array $columnSpan = 'full';
}
