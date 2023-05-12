<?php

declare(strict_types=1);

namespace pxlrbt\FilamentSpotlight\Commands;

use Filament\Resources\Resource;
use Illuminate\Database\Connection;
use Illuminate\Support\{Arr, Collection, Str};
use Filament\Resources\Pages\{CreateRecord, EditRecord, Page, ViewRecord};
use Illuminate\Database\Eloquent\{Builder, Collection as EloquentCollection, Model};
use LivewireUI\Spotlight\{Spotlight, SpotlightCommand, SpotlightCommandDependencies, SpotlightCommandDependency, SpotlightSearchResult};

class ResourceCommand extends SpotlightCommand
{
    protected Page $page;

    protected Resource $resource;

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

    public function dependencies(): ?SpotlightCommandDependencies
    {
        if ( ! $this->hasDependencies()) {
            return null;
        }

        return SpotlightCommandDependencies::collection()->add(
            SpotlightCommandDependency::make('record')->setPlaceholder(__('Search for a :record', ['record' => $this->resource::getModelLabel()]))
        );
    }

    public function execute(Spotlight $spotlight, $record = null): void
    {
        $spotlight->redirect($this->getUrl($record));
    }

    public function getId(): string
    {
        return md5($this->resource::class . $this->page::class);
    }

    public function getName(): string
    {
        return $this->resource::getBreadcrumb() . ' – ' . $this->page->getBreadcrumb();
    }

    public function getUrl(null|int|string $recordKey): string
    {
        return $this->resource::getUrl($this->key, $recordKey);
    }

    public function searchRecord($query): EloquentCollection|Collection|array
    {
        $resource = $this->resource;
        $searchQuery = $query;
        $query = $resource::getEloquentQuery();

        foreach (explode(' ', $searchQuery) as $searchQueryWord) {
            $query->where(function (Builder $query) use ($searchQueryWord, $resource): void {
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
                    ->map(fn ($value, $key) => $key . ': ' . $value)
                    ->join(' – ')
            ));
    }

    public function shouldBeShown(): bool
    {
        return match (true) {
            $this->page instanceof CreateRecord => $this->resource::canCreate(),
            default => $this->resource::canViewAny(),
        };
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

    protected function hasDependencies(): bool
    {
        return match (true) {
            $this->page instanceof EditRecord, $this->page instanceof ViewRecord => true,
            default => false,
        };
    }
}
