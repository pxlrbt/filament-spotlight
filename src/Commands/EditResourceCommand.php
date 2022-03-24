<?php

namespace pxlrbt\FilamentSpotlight\Commands;

use Filament\GlobalSearch\GlobalSearchResult;
use Filament\Resources\Resource;
use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;
use LivewireUI\Spotlight\SpotlightCommandDependencies;
use LivewireUI\Spotlight\SpotlightCommandDependency;
use LivewireUI\Spotlight\SpotlightSearchResult;

/**
 * @property  class-string<resource>  $resource
 */
class EditResourceCommand extends SpotlightCommand
{
    /**
     * @param  class-string<resource>  $resource
     */
    public function __construct(
        protected string $name,
        protected string $resource,
        protected bool $shouldBeShown = true,
    ) {
        //
    }

    public function getId(): string
    {
        return md5($this->resource . '.edit');
    }

    public function shouldBeShown(): bool
    {
        return $this->shouldBeShown;
    }

    public function dependencies(): ?SpotlightCommandDependencies
    {
        return SpotlightCommandDependencies::collection()->add(
            SpotlightCommandDependency::make('record')->setPlaceholder('Search for a ' . $this->resource::getLabel() . '.')
        );
    }

    public function searchRecord($query)
    {
        $results = $this->resource::getGlobalSearchResults($query)
            ->map(fn (GlobalSearchResult $result) => new SpotlightSearchResult(
                $result->url,
                $result->title,
                collect($result->details)
                    ->map(fn ($value, $key) => $key . ': ' . $value)
                    ->join(' – ')
            ));

        return $results;
    }

    public function execute(Spotlight $spotlight, $record): void
    {
        $spotlight->redirect($record);
    }
}
