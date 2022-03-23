<?php

namespace pxlrbt\FilamentSpotlight;

use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;

class FilamentSpotlightCommand extends SpotlightCommand
{
    public function __construct(
        protected string $name,
        protected string $url,
        protected bool $shouldBeShown = true,
    ) {
        //
    }

    public function getId(): string
    {
        return md5($this->url);
    }

    public function shouldBeShown(): bool
    {
        return $this->shouldBeShown;
    }

    public function execute(Spotlight $spotlight): void
    {
        $spotlight->redirect($this->url);
    }
}
