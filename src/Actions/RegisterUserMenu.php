<?php

namespace pxlrbt\FilamentSpotlight\Actions;

use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
use LivewireUI\Spotlight\Spotlight;
use pxlrbt\FilamentSpotlight\Commands\PageCommand;

class RegisterUserMenu
{
    public function __invoke()
    {
        /**
         * @var array<UserMenuItem> $items
         */
        $items = Filament::getUserMenuItems();

        foreach ($items as $key => $item) {
            $name = $this->getName($key, $item);
            $url = $this->getUrl($key, $item);

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

    protected function getName(string $key, UserMenuItem $item): string
    {
        return match ($key) {
            'account' => $item->getLabel() ?? __('Your Account'),
            'logout' => $item->getLabel() ?? __('filament::layout.buttons.logout.label'),
            default => $item->getLabel()
        };
    }

    protected function getUrl(string $key, UserMenuItem $item): string
    {
        return match ($key) {
            'logout' => $item->getUrl() ?? route('filament.auth.logout'),
            default => $item->getUrl()
        };
    }
}
