<?php

namespace pxlrbt\FilamentSpotlight\Actions;

use Filament\Facades\Filament;
use LivewireUI\Spotlight\Spotlight;
use pxlrbt\FilamentSpotlight\FilamentSpotlightCommand;

class RegisterPages
{
    public function __invoke()
    {
        $pages = Filament::getPages();

        foreach ($pages as $page) {
            $command = new FilamentSpotlightCommand(
                name: invade(new $page())->getTitle(),
                url: $page::getUrl()
            );

            Spotlight::$commands[$command->getId()] = $command;
        }
    }
}
