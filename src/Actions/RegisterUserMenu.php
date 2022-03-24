<?php

namespace pxlrbt\FilamentSpotlight\Actions;

use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
use LivewireUI\Spotlight\Spotlight;
use pxlrbt\FilamentSpotlight\Commands\DefaultCommand;

class RegisterUserMenu
{
    public function __invoke()
    {
        /**
         * @var array<UserMenuItem> $items
         */
        $items = Filament::getUserMenuItems();

        foreach ($items as $key => $item) {
            $label = match($key) {
                'account' => $item->getLabel() ?? __('Your Account'),
                'logout' => $item->getLabel() ?? __('filament::layout.buttons.logout.label'),
                default => $item->getLabel()
            };

            $url = match($key) {
                'logout' => $item->getUrl() ?? route('filament.auth.logout'),
                default => $item->getUrl()
            };

            if (blank($label) || blank($url)) {
                continue;
            }

            $command = new DefaultCommand(
                name: $label,
                url: $url,
            );

            Spotlight::$commands[$command->getId()] = $command;
        }
    }
}
