<?php

declare(strict_types=1);

namespace pxlrbt\FilamentSpotlight\Actions;

use Exception;
use Filament\Facades\Filament;
use LivewireUI\Spotlight\Spotlight;
use Illuminate\Support\Facades\Auth;
use Wallo\FilamentCompanies\FilamentCompanies;
use pxlrbt\FilamentSpotlight\Commands\PageCommand;
use Wallo\FilamentCompanies\Pages\Companies\CompanySettings;

class RegisterPages
{
    public function __invoke(): void
    {
        $pages = Filament::getPages();

        foreach ($pages as $page) {
            $name = \Livewire\invade(new $page())->getTitle();
            if (str_contains(@CompanySettings::class, $page)) {
                // filament-companies route params fixed
                if (@FilamentCompanies::hasCompanyFeatures() && @Auth::user()?->currentCompany) {
                    $url = $page::getUrl([@Auth::user()?->currentCompany]);
                }
            } else {
                try {
                    // Custom page error missing required parameter
                    $url = $page::getUrl();
                } catch (Exception $e) {
                    // ignore
                }
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
