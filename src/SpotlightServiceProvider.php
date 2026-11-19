<?php

namespace pxlrbt\FilamentSpotlight;

use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;

class SpotlightServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'filament-spotlight');

        config()->set('livewire-ui-spotlight.commands', []);

        // Loaded on request: Filament prints every other registered asset on every page of every panel, including
        // the panels that do not use the plugin. The plugin prints it on its own panels, with the component.
        FilamentAsset::register([
            Js::make('spotlight-js', __DIR__.'/../resources/dist/js/spotlight.js')->loadedOnRequest(),
        ], package: 'pxlrbt/filament-spotlight');
    }
}
