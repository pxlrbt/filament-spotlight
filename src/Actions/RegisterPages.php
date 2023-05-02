<?php

namespace pxlrbt\FilamentSpotlight\Actions;

use Filament\Facades\Filament;
use LivewireUI\Spotlight\Spotlight;
use pxlrbt\FilamentSpotlight\Commands\PageCommand;

class RegisterPages
{
    public function __invoke()
    {
        $pages = Filament::getPages();

        foreach ($pages as $page) {
            $name = \Livewire\invade(new $page())->getTitle();
            if (str_contains('Wallo\FilamentCompanies\Pages\Companies\CompanySettings', $page)) {
                if (@\Wallo\FilamentCompanies\FilamentCompanies::hasCompanyFeatures()) {
                    $url = $page::getUrl([@\Illuminate\Support\Facades\Auth::user()?->currentCompany]);                    
                }
            } else {
                $url = $page::getUrl();
            }

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
}
