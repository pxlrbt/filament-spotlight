<?php

namespace pxlrbt\FilamentSpotlight;

use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\PluginServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;

class FilamentSpotlightServiceProvider extends PluginServiceProvider
{
    protected array $styles = [
        'spotlight' => __DIR__ . '/../resources/dist/css/spotlight.css',
    ];

    protected array $scripts = [
        'spotlight' => __DIR__ . '/../resources/dist/js/spotlight.js',
    ];

    public function configurePackage(Package $package): void
    {
        $package->name('filament-spotlight');

        if (! $this->isFilament()) {
            return;
        }

        Config::set('livewire-ui-spotlight.include_js', false);
        Config::set('livewire-ui-spotlight.commands', []);

        Config::push('filament.middleware.base', FilamentSpotlightMiddleware::class);
    }

    public function packageBooted(): void
    {
        if (! $this->isFilament()) {
            return;
        }

        /**
         * @var Router $router
         */
        $router = $this->app['router'];
        // Add DispatchServingFilamentEvent::class?
        $router->pushMiddlewareToGroup(config('livewire.middleware_group'), DispatchServingFilamentEvent::class);
        $router->pushMiddlewareToGroup(config('livewire.middleware_group'), FilamentSpotlightMiddleware::class);
    }

    protected function isFilament(): bool
    {
        $path = Livewire::isLivewireRequest()
            ? request('fingerprint.path')
            : request()->path();

        $filamentPath = config('filament.path');

        return Str::of($path)
            ->prepend('/')
            ->replace('//', '/')
            ->startsWith($filamentPath);
    }
}
