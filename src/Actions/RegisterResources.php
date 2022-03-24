<?php

namespace pxlrbt\FilamentSpotlight\Actions;

use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Support\Str;
use LivewireUI\Spotlight\Spotlight;
use pxlrbt\FilamentSpotlight\Commands\DefaultCommand;
use pxlrbt\FilamentSpotlight\Commands\EditResourceCommand;

class RegisterResources
{
    public function __invoke()
    {
        $resources = Filament::getResources();

        foreach ($resources as $resource) {
            $pages = $resource::getPages();

            foreach ($pages as $key => $page) {
                if ($key === 'edit') {
                    $command = new EditResourceCommand(
                        name: $resource::getBreadcrumb() . ' – ' . (new $page['class']())->getBreadcrumb(),
                        resource: $resource
                    );
                } else {
                    if (Str::contains($page['route'], '{')) {
                        continue;
                    }

                    $command = new DefaultCommand(
                        name: $resource::getBreadcrumb() . ' – ' . (new $page['class']())->getBreadcrumb(),
                        url: $resource::getUrl($key),
                        shouldBeShown: $this->shouldBeShown($resource, $key),
                    );
                }

                Spotlight::$commands[$command->getId()] = $command;
            }
        }
    }

    /**
     * @param  class-string<resource>  $resource
     */
    private function shouldBeShown(string $resource, int|string $key): bool
    {
        return match ($key) {
            'index', 'edit', 'view' => $resource::canViewAny(),
            'create' => $resource::canCreate(),
            default => false,
        };
    }
}
