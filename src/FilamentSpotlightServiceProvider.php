<?php

declare(strict_types=1);

namespace pxlrbt\FilamentSpotlight;

use Filament\Facades\Filament;
use Filament\PluginServiceProvider;
use Filament\Events\ServingFilament;
use Spatie\LaravelPackageTools\Package;
use Illuminate\Support\Facades\{Blade, Config, Event};
use pxlrbt\FilamentSpotlight\Actions\{RegisterPages, RegisterResources, RegisterUserMenu};

class FilamentSpotlightServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament-spotlight';

    protected array $beforeCoreScripts = [
        'spotlight' => __DIR__ . '/../resources/dist/js/spotlight.js',
    ];

    protected array $styles = [
        'spotlight' => __DIR__ . '/../resources/dist/css/spotlight.css',
    ];

    public function packageConfiguring(Package $package): void
    {
        Config::set('livewire-ui-spotlight.include_js', false);
        Config::set('livewire-ui-spotlight.commands', []);

        Event::listen(ServingFilament::class, [$this, 'registerSpotlight']);
    }

    public function registerSpotlight(ServingFilament $event): void
    {
        if ( ! Filament::auth()->check()) {
            return;
        }

        (new RegisterPages())();
        (new RegisterResources())();
        (new RegisterUserMenu())();

        Filament::registerRenderHook('scripts.end', fn () => Blade::render("@livewire('livewire-ui-spotlight')"));
    }
}
