<?php

namespace pxlrbt\FilamentSpotlight;

use Closure;
use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use LivewireUI\Spotlight\Spotlight;

class FilamentSpotlightMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $this->registerPages();
        $this->registerResources();
        $this->registerUserMenu();

        $this->injectLivewireComponent();

        return $next($request);
    }

    public function registerPages(): void
    {
        $pages = Filament::getPages();

        foreach ($pages as $page) {
            $command = new FilamentSpotlightCommand(
                name: invade(new $page())->getTitle(),
                url: $page::getUrl()
            );

            Spotlight::$commands[$command->getId()] = $command;
        }
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

                $command = new FilamentSpotlightCommand(
                    name: $resource::getBreadcrumb() . ' – ' . (new $page['class']())->getBreadcrumb(),
                    url: $resource::getUrl($key),
                    shouldBeShown: match ($key) {
                        'index' => $resource::canViewAny(),
                        'view' => $resource::canView(),
                        'create' => $resource::canCreate(),
                        default => false,
                    }
                );

                Spotlight::$commands[$command->getId()] = $command;
            }
        }
    }

    public function registerUserMenu(): void
    {
        /**
         * @var array<UserMenuItem> $items
         */
        $items = Filament::getUserMenuItems();

        foreach ($items as $item) {
            $command = new FilamentSpotlightCommand(
                name: $item->getLabel(),
                url: $item->getUrl(),
            );

            Spotlight::$commands[$command->getId()] = $command;
        }
    }

    protected function injectLivewireComponent()
    {
        app('view')->startPush('scripts', Blade::render("@livewire('livewire-ui-spotlight')"));
    }
}
