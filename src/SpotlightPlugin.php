<?php

namespace pxlrbt\FilamentSpotlight;

use Filament\Contracts\Plugin;
use Filament\Events\TenantSet;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use pxlrbt\FilamentSpotlight\Actions\RegisterPages;
use pxlrbt\FilamentSpotlight\Actions\RegisterResources;
use pxlrbt\FilamentSpotlight\Actions\RegisterUserMenu;

class SpotlightPlugin implements Plugin
{
    public static string $name = 'pxlrbt/filament-spotlight';

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return self::$name;
    }

    public function register(Panel $panel): void
    {
        // The script is loaded on request (see the service provider), so it is printed here, with the component,
        // only on the panels that use the plugin.
        $panel->renderHook(
            'panels::scripts.after',
            fn () => collect(FilamentAsset::getScripts([self::$name]))
                ->map(fn (Js $script) => $script->getHtml()->toHtml())
                ->implode('')
                .Blade::render("@livewire('livewire-ui-spotlight')")
        );
    }

    public function boot(Panel $panel): void
    {
        Filament::serving(function () use ($panel) {
            if (! Filament::auth()->check()) {
                return;
            }
            config()->set('livewire-ui-spotlight.include_js', false);

            if (Filament::hasTenancy()) {
                Event::listen(TenantSet::class, function () use ($panel) {
                    self::registerNavigation($panel);
                });
            } else {
                self::registerNavigation($panel);
            }

        });
    }

    public static function registerNavigation($panel)
    {
        RegisterPages::boot($panel);
        RegisterResources::boot($panel);
        RegisterUserMenu::boot($panel);
    }
}
