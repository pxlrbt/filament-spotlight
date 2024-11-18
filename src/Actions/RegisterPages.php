<?php

namespace pxlrbt\FilamentSpotlight\Actions;

use Filament\Pages\Page;
use Filament\Panel;
use LivewireUI\Spotlight\Spotlight;
use pxlrbt\FilamentSpotlight\Commands\PageCommand;

class RegisterPages
{
    public static function boot(Panel $panel)
    {
        $pages = $panel->getPages();

        foreach ($pages as $pageClass) {

            /**
             * @var Page $page
             */
            $page = new $pageClass;

            if (self::hasParameters($page::getSlug())) {
                continue;
            }

            if (method_exists($page, 'shouldRegisterSpotlight') && $page::shouldRegisterSpotlight() === false) {
                continue;
            }

            $name = collect([
                $page->getNavigationGroup(),
                $page->getTitle(),
            ])->filter()->join(' / ');

            $url = $page::getUrl();

            if (blank($name) || blank($url)) {
                continue;
            }

            $command = new PageCommand(
                name: $name,
                url: $url
            );

            Spotlight::$commands[$command->getId()] = $command;
        }
    }

    private static function hasParameters(string $slug): bool
    {
        return preg_match('/{[^}]+}/', $slug) === 1;
    }
}
