<?php

namespace pxlrbt\FilamentSpotlight\Actions;

use Exception;
use Filament\Facades\Filament;
use LivewireUI\Spotlight\Spotlight;
use pxlrbt\FilamentSpotlight\Commands\PageCommand;

class RegisterPages
{
    public function __invoke()
    {
        $pages = Filament::getPages();

        foreach ($pages as $page) {
            try {
                $name = \Livewire\invade(new $page())->getTitle();
                $url = $page::getUrl();

                if (blank($name) || blank($url)) {
                    continue;
                }

                $command = new PageCommand(
                    name: $name,
                    url: $url
                );

                Spotlight::$commands[$command->getId()] = $command;
            } catch (Exception $e) {
                  // Fail gracefully if getUrl() requires params
            }
        }
    }
}
