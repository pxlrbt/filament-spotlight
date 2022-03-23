<?php

namespace pxlrbt\FilamentSpotlight;

use Filament\Facades\Filament;
use Filament\PluginServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;
use Spatie\LaravelPackageTools\Package;

class FilamentSpotlightServiceProvider extends PluginServiceProvider
{
    protected array $styles = [
        'spotlight' => __DIR__ . '/../resources/dist/css/spotlight.css',
    ];

    public function configurePackage(Package $package): void
    {
        $package->name('filament-spotlight');

        Config::set('livewire-ui-spotlight.commands', []);
        Config::push('filament.middleware.base', InjectSpotlightMiddleware::class);
    }

    public function packageBooted(): void
    {
        $this->registerPages();
        $this->registerResources();
    }

    public function registerPages(): void
    {
        $pages = Filament::getPages();

        foreach ($pages as $page) {
            $this->registerPage($page);
        }
    }

    public function registerPage($page)
    {
        $command = new class($page) extends SpotlightCommand {
            public function __construct(
                protected $page,
            ) {
                $this->name = invade(new $page())->getTitle();
            }

            public function getId(): string
            {
                return md5($this->page);
            }

            public function shouldBeShown(): bool
            {
                return true;
            }

            public function execute(Spotlight $spotlight): void
            {
                $spotlight->redirect($this->page::getUrl());
            }
        };

        Spotlight::$commands[] = $command;
    }

    public function registerResourcePage($resource, $page, $key)
    {
        $command = new class($resource, $page, $key) extends SpotlightCommand {
            public function __construct(
                protected string $resource,
                protected $page,
                protected $key,
            ) {
                $this->name = $resource::getBreadcrumb() . ' – ' . (new $page['class']())->getBreadcrumb();
            }

            public function getId(): string
            {
                return md5($this->page['class']);
            }

            public function shouldBeShown(): bool
            {
                return true;
            }

            public function execute(Spotlight $spotlight): void
            {
                $spotlight->redirect($this->resource::getUrl($this->key));
            }
        };

        Spotlight::$commands[] = $command;
    }

    public function registerResources(): void
    {
        $resources = Filament::getResources();

        foreach ($resources as $resource) {
            $pages = $resource::getPages();

            foreach ($pages as $key => $page) {
                if (Str::contains($page['route'], '{')) {
                    continue;
                }
                $this->registerResourcePage($resource, $page, $key);
            }
        }
    }
}
