<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\View\Components\Modal;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Modal::closedByClickingAway(false);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentAsset::register([
            Js::make('move-to-another-text-field', __DIR__ . '/../../resources/js/move_to_another_text_field.js'),
        ]);
    }
}
