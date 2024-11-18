<?php

namespace pxlrbt\FilamentSpotlight;

use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;

class SpotlightServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'filament-spotlight');

        config()->set('livewire-ui-spotlight.commands', []);

        FilamentAsset::register([
            Css::make('spotlight-css', __DIR__.'/../resources/dist/css/spotlight.css'),
            Js::make('spotlight-js', __DIR__.'/../resources/dist/js/spotlight.js'),
        ], package: 'pxlrbt/filament-spotlight');
    }
}
