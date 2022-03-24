<?php

namespace pxlrbt\FilamentSpotlight\Actions;

use Filament\Facades\Filament;
use Illuminate\Support\Str;
use LivewireUI\Spotlight\Spotlight;
use pxlrbt\FilamentSpotlight\FilamentSpotlightCommand;

class RegisterResources
{
    public function __invoke()
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
}
