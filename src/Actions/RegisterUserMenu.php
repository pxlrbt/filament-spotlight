<?php

namespace pxlrbt\FilamentSpotlight\Actions;

use Filament\Facades\Filament;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use LivewireUI\Spotlight\Spotlight;
use pxlrbt\FilamentSpotlight\Commands\PageCommand;

class RegisterUserMenu
{
    public static function boot(Panel $panel)
    {
        $self = new static;
        /**
         * @var array<MenuItem> $items
         */
        $items = $panel->getUserMenuItems();

        foreach ($items as $key => $item) {
            $name = $self->getName($key, $item);
            $url = $self->getUrl($key, $item);

            if (blank($name) || blank($url)) {
                continue;
            }

            $command = new PageCommand(
                name: $name,
                url: $url,
            );

            Spotlight::$commands[$command->getId()] = $command;
        }
    }

    protected function getName(string $key, MenuItem $item): ?string
    {
        return match ($key) {
            'account' => $item->getLabel() ?? __('Your Account'),
            'logout' => $item->getLabel() ?? __('filament::layout.buttons.logout.label'),
            default => $item->getLabel()
        };
    }

    protected function getUrl(string $key, MenuItem $item): ?string
    {
        return match ($key) {
            'logout' => $item->getUrl() ?? Filament::getLogoutUrl(),
            default => $item->getUrl()
        };
    }
}
