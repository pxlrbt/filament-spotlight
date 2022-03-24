<?php

namespace pxlrbt\FilamentSpotlight\Actions;

use Illuminate\Support\Facades\Blade;

class InjectSpotlightComponent
{
    public function __invoke()
    {
        app('view')->startPush('beforeCoreScripts', Blade::render("@livewire('livewire-ui-spotlight')"));
    }
}
