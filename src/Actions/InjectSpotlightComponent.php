<?php

namespace pxlrbt\FilamentSpotlight\Actions;

use Blade;

class InjectSpotlightComponent
{
    public function __invoke()
    {
        app('view')->startPush('beforeCoreScripts', Blade::render("@livewire('livewire-ui-spotlight')"));
    }
}
