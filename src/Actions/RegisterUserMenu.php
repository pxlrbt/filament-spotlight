<?php

namespace pxlrbt\FilamentSpotlight\Actions;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use LivewireUI\Spotlight\Spotlight;
use pxlrbt\FilamentSpotlight\Commands\PageCommand;

class RegisterUserMenu
{
    public static function boot(Panel $panel): void
    {
        $self = new static;

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

    protected function getName(string $key, Action|MenuItem $item): ?string
    {
        return match ($key) {
            'account' => $item->getLabel() ?? __('filament-spotlight::spotlight.account'),
            'logout' => $item->getLabel() ?? __('filament-panels::layout.actions.logout.label'),
            default => $item->getLabel()
        };
    }

    protected function getUrl(string $key, Action|MenuItem $item): ?string
    {
        return match ($key) {
            'logout' => $item->getUrl() ?? Filament::getLogoutUrl(),
            default => $item->getUrl()
        };
    }
}
