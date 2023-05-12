<?php

declare(strict_types=1);

namespace pxlrbt\FilamentSpotlight\Commands;

use LivewireUI\Spotlight\{Spotlight, SpotlightCommand};

class PageCommand extends SpotlightCommand
{
    public function __construct(
        protected string $name,
        protected string $url,
        protected bool $shouldBeShown = true,
    ) {

    }

    public function execute(Spotlight $spotlight): void
    {
        $spotlight->redirect($this->url);
    }

    public function getId(): string
    {
        return md5($this->url);
    }

    public function shouldBeShown(): bool
    {
        return $this->shouldBeShown;
    }
}
