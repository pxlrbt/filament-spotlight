<?php

namespace pxlrbt\FilamentSpotlight\Actions;

use Filament\Panel;
use Filament\Resources\Pages\PageRegistration;
use LivewireUI\Spotlight\Spotlight;
use pxlrbt\FilamentSpotlight\Commands\ResourceCommand;

class RegisterResources
{
    public static function boot(Panel $panel)
    {
        $resources = $panel->getResources();

        foreach ($resources as $resource) {
            $pages = $resource::getPages();

            foreach ($pages as $key => $page) {
                /**
                 * @var PageRegistration $page
                 */
                if (blank($key) || blank($page->getPage())) {
                    continue;
                }

                $command = new ResourceCommand(
                    resource: $resource,
                    page: $page->getPage(),
                    key: $key,
                );

                Spotlight::$commands[$command->getId()] = $command;
            }
        }
    }
}
