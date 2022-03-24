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

        foreach ($items as $item) {
            $command = new DefaultCommand(
                name: $item->getLabel(),
                url: $item->getUrl(),
            );

            Spotlight::$commands[$command->getId()] = $command;
        }
    }
}
