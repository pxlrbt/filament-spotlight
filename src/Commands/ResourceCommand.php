<?php

namespace pxlrbt\FilamentSpotlight\Commands;

use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\Resource;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;
use LivewireUI\Spotlight\SpotlightCommandDependencies;
use LivewireUI\Spotlight\SpotlightCommandDependency;
use LivewireUI\Spotlight\SpotlightSearchResult;

class ResourceCommand extends SpotlightCommand
{
    protected Resource $resource;

    protected Page $page;

    /**
     * @param  class-string<resource>  $resource
     * @param  class-string<Page>  $page
     */
    public function __construct(
        string $resource,
        string $page,
        protected string $key,
    ) {
        $this->resource = new $resource();
        $this->page = new $page();
    }

    public function getId(): string
    {
        return md5($this->resource::class.$this->page::class);
    }

    public function getName(): string
    {
        return collect([
            $this->resource::getNavigationGroup(),
            $this->resource::getBreadcrumb(),
            $this->page::getNavigationLabel(),
        ])
            ->filter()
            ->join(' / ');
    }

    public function getUrl(null|int|string $recordKey): string
    {
        return $this->resource::getUrl($this->key, $recordKey ? ['record' => $recordKey] : []);
    }

    public function shouldBeShown(): bool
    {
        return match (true) {
            $this->page instanceof CreateRecord => $this->resource::canCreate(),
            default => $this->resource::canViewAny(),
        };
    }

    protected function hasDependencies(): bool
    {
        return match (true) {
            $this->page instanceof EditRecord => true,
            $this->page instanceof ViewRecord => true,
            $this->page instanceof ManageRelatedRecords => true,
            default => false,
        };
    }

    public function dependencies(): ?SpotlightCommandDependencies
    {
        if (! $this->hasDependencies()) {
            return null;
        }

        return SpotlightCommandDependencies::collection()->add(
            SpotlightCommandDependency::make('record')->setPlaceholder(__('Search for a :record', ['record' => $this->resource::getModelLabel()]))
        );
    }

    public function searchRecord($query): EloquentCollection|Collection|array
    {
        $resource = $this->resource;
        $searchQuery = $query;
        $query = $resource::getEloquentQuery();

        foreach (explode(' ', $searchQuery) as $searchQueryWord) {
            $query->where(function (Builder $query) use ($searchQueryWord, $resource) {
                $isFirst = true;

                foreach ($resource::getGloballySearchableAttributes() as $attributes) {
                    static::applyGlobalSearchAttributeConstraint($query, Arr::wrap($attributes), $searchQueryWord, $isFirst);
                }
            });
        }

        return $query
            ->limit(50)
            ->get()
            ->map(fn (Model $record) => new SpotlightSearchResult(
                $record->getKey(),
                $resource::getGlobalSearchResultTitle($record),
                collect($resource::getGlobalSearchResultDetails($record))
                    ->map(fn ($value, $key) => $key.': '.$value)
                    ->join(' – ')
            ));
    }

    protected static function applyGlobalSearchAttributeConstraint(Builder $query, array $searchAttributes, string $searchQuery, bool &$isFirst): Builder
    {
        /** @var Connection $databaseConnection */
        $databaseConnection = $query->getConnection();

        $searchOperator = match ($databaseConnection->getDriverName()) {
            'pgsql' => 'ilike',
            default => 'like',
        };

        foreach ($searchAttributes as $searchAttribute) {
            $whereClause = $isFirst ? 'where' : 'orWhere';

            $query->when(
                Str::of($searchAttribute)->contains('.'),
                fn ($query) => $query->{"{$whereClause}Relation"}(
                    (string) Str::of($searchAttribute)->beforeLast('.'),
                    (string) Str::of($searchAttribute)->afterLast('.'),
                    $searchOperator,
                    "%{$searchQuery}%",
                ),
                fn ($query) => $query->{$whereClause}(
                    $searchAttribute,
                    $searchOperator,
                    "%{$searchQuery}%",
                ),
            );

            $isFirst = false;
        }

        return $query;
    }

    public function execute(Spotlight $spotlight, $record = null): void
    {
        $spotlight->redirect($this->getUrl($record));
    }
}
