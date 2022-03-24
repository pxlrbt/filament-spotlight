<?php

namespace pxlrbt\FilamentSpotlight\Actions;

use Filament\Facades\Filament;
use LivewireUI\Spotlight\Spotlight;
use pxlrbt\FilamentSpotlight\Commands\DefaultCommand;

class RegisterPages
{
    public function __invoke()
    {
        $pages = Filament::getPages();

        foreach ($pages as $page) {
            $command = new DefaultCommand(
                name: invade(new $page())->getTitle(),
                url: $page::getUrl()
            );

            Spotlight::$commands[$command->getId()] = $command;
        }
    }
}
