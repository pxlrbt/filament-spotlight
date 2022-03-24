<?php

namespace pxlrbt\FilamentSpotlight\Actions;

use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
use LivewireUI\Spotlight\Spotlight;
use pxlrbt\FilamentSpotlight\FilamentSpotlightCommand;

class RegisterUserMenu
{
    public function __invoke()
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
}
