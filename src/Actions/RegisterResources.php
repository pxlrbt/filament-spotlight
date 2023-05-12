<?php

declare(strict_types=1);

namespace pxlrbt\FilamentSpotlight\Actions;

use Filament\Facades\Filament;
use LivewireUI\Spotlight\Spotlight;
use pxlrbt\FilamentSpotlight\Commands\ResourceCommand;

class RegisterResources
{
    public function __invoke(): void
    {
        $resources = Filament::getResources();

        foreach ($resources as $resource) {
            $pages = $resource::getPages();

            foreach ($pages as $key => $page) {
                if (blank($key) || blank($page['class'])) {
                    continue;
                }

                $command = new ResourceCommand(
                    resource: $resource,
                    page: $page['class'],
                    key: $key,
                );

                Spotlight::$commands[$command->getId()] = $command;
            }
        }
    }
}
