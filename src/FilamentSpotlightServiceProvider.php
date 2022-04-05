<?php

namespace pxlrbt\FilamentSpotlight;

use Filament\Events\ServingFilament;
use Filament\Facades\Filament;
use Filament\PluginServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use pxlrbt\FilamentSpotlight\Actions\InjectSpotlightComponent;
use pxlrbt\FilamentSpotlight\Actions\RegisterPages;
use pxlrbt\FilamentSpotlight\Actions\RegisterResources;
use pxlrbt\FilamentSpotlight\Actions\RegisterUserMenu;
use Spatie\LaravelPackageTools\Package;

class FilamentSpotlightServiceProvider extends PluginServiceProvider
{
    public static string $name = 'Filament Spotlight';

    protected array $styles = [
        'spotlight' => __DIR__ . '/../resources/dist/css/spotlight.css',
    ];

    protected array $beforeCoreScripts = [
        'spotlight' => __DIR__ . '/../resources/dist/js/spotlight.js',
    ];

    public function packageConfiguring(Package $package): void
    {
        Config::set('livewire-ui-spotlight.include_js', false);
        Config::set('livewire-ui-spotlight.commands', []);

        Event::listen(ServingFilament::class, [$this, 'registerSpotlight']);
    }

    public function registerSpotlight(ServingFilament $event): void
    {
        if (! Filament::auth()->check()) {
            return;
        }

        (new RegisterPages())();
        (new RegisterResources())();
        (new RegisterUserMenu())();

        (new InjectSpotlightComponent())();
    }
}
