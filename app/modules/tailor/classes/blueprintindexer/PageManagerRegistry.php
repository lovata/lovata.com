<?php namespace Tailor\Classes\BlueprintIndexer;

use Cms;
use Site;
use Cms\Classes\Page;
use Tailor\Classes\Blueprint\EntryBlueprint;
use Tailor\Models\EntryRecord;

/**
 * PageManagerRegistry
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
trait PageManagerRegistry
{
    /**
     * listPrimaryNavigation
     */
    public function listPageManagerTypes(): array
    {
        $types = [];

        // Sections
        foreach (EntryBlueprint::listInProject() as $blueprint) {
            if ($typeCode = $this->pageManagerBlueprintToType($blueprint)) {
                if ($blueprint->usePageFinder()) {
                    $types[$typeCode] = $blueprint->getMessage('pagefinderItemType', ":name Entry", ['name' => $blueprint->name]);
                }

                if ($blueprint->usePageFinder('list')) {
                    $types['list-'.$typeCode] = [
                        $blueprint->getMessage('pagefinderListType', "All :name Entries", ['name' => $blueprint->name]),
                        true
                    ];
                }
            }
        }

        return $types;
    }

    /**
     * getPageManagerTypeInfo
     */
    public function getPageManagerTypeInfo($type): array
    {
        [$model, $query] = $this->pageManagerTypeToModel($type);
        if (!$model) {
            return [];
        }

        $result = [];

        if (!starts_with($type, 'list-')) {
            $result['references'] = $this->listRecordOptionsForPageInfo($model, $query);
        }

        $result['cmsPages'] = $this->listBlueprintCmsPagesForPageInfo($model);

        return $result;
    }

    /**
     * listBlueprintCmsPagesForPageInfo
     */
    protected function listBlueprintCmsPagesForPageInfo($model)
    {
        $handle = $model->blueprint->handle ?? $model->blueprint_uuid;
        return Page::whereComponent('section', 'handle', $handle)->all();
    }

    /**
     * listRecordOptionsForPageInfo
     */
    protected function listRecordOptionsForPageInfo($model, $query)
    {
        $records = $model->isClassInstanceOf(\October\Contracts\Database\TreeInterface::class)
            ? $query->getNested()
            : $query->get();

        $iterator = function($records) use (&$iterator) {
            $result = [];
            foreach ($records as $record) {
                $id = $record->site_root_id ?: $record->id;
                if (!$record->children) {
                    $result[$id] = $record->title;
                }
                else {
                    $result[$id] = [
                        'title' => $record->title,
                        'items' => $iterator($record->children)
                    ];
                }
            }
            return $result;
        };

        return $iterator($records);
    }

    /**
     * resolvePageManagerItem
     */
    public function resolvePageManagerItem($type, $item, $url, $theme): array
    {
        if (starts_with($type, 'list-')) {
            return $this->resolvePageManagerItemAsList($type, $item, $url, $theme);
        }

        return $this->resolvePageManagerItemAsReference($type, $item, $url, $theme);
    }

    /**
     * resolvePageManagerItemAsList
     */
    protected function resolvePageManagerItemAsList($type, $item, $url, $theme): array
    {
        [$model, $query] = $this->pageManagerTypeToModel($type);
        if (!$model) {
            return [];
        }

        $result = [];

        $records = $model->isClassInstanceOf(\October\Contracts\Database\TreeInterface::class)
            ? $query->getNested()
            : $query->get();

        $recurse = $model->isEntryStructure() && $item->nesting;

        $result['items'] = $this->resolvePageManagerItemAsChildren($records, $item, $theme, $url, $recurse);

        return $result;
    }

    /**
     * resolvePageManagerItemAsReference
     */
    protected function resolvePageManagerItemAsReference($type, $item, $url, $theme): array
    {
        [$model, $query] = $this->pageManagerTypeToModel($type);
        if (!$model) {
            return [];
        }

        if ($model->isClassInstanceOf(\October\Contracts\Database\MultisiteInterface::class)) {
            $record = $query->applyOtherSiteRoot($item->reference)->first();
        }
        else {
            $record = $query->find($item->reference);
        }

        if (!$record) {
            return [];
        }

        $pageUrl = $this->getPageManagerPageUrl($item->cmsPage, $record, $theme);

        $result = [
            'url' => $pageUrl,
            'isActive' => $pageUrl == $url,
            'mtime' => $record->updated_at,
        ];

        if ($item->sites) {
            $result['sites'] = $this->getPageManagerSites($item->cmsPage, $record, $theme);
        }

        if (!$model->isEntryStructure() || !$item->nesting) {
            return $result;
        }

        $result['items'] = $this->resolvePageManagerItemAsChildren($record->children, $item, $theme, $url);

        return $result;
    }

    /**
     * resolvePageManagerItemAsChildren
     */
    protected function resolvePageManagerItemAsChildren($children, $item, $theme, $url, $recursive = true)
    {
        $branch = [];

        foreach ($children as $child) {
            $childUrl = $this->getPageManagerPageUrl($item->cmsPage, $child, $theme);

            $childItem = [
                'url' => $childUrl,
                'isActive' => $childUrl == $url,
                'title' => $child->title,
                'mtime' => $child->updated_at,
            ];

            if ($item->sites) {
                $childItem['sites'] = $this->getPageManagerSites($item->cmsPage, $child, $theme);
            }

            if ($recursive && $child->children) {
                $childItem['items'] = $this->resolvePageManagerItemAsChildren(
                    $child->children,
                    $item,
                    $theme,
                    $url,
                    $recursive
                );
            }

            $branch[] = $childItem;
        }

        return $branch;
    }

    /**
     * getPageManagerPageUrl
     */
    protected static function getPageManagerPageUrl($pageCode, $record, $theme)
    {
        $url = Cms::pageUrl($pageCode, [
            'id' => $record->id,
            'slug' => $record->slug,
            'fullslug' => $record->fullslug
        ]);

        return $url;
    }

    /**
     * getPageManagerPageUrl
     */
    protected static function getPageManagerSites($pageCode, $record, $theme): array
    {
        if (
            !Site::hasMultiSite() ||
            !$record ||
            !$record->isClassInstanceOf(\October\Contracts\Database\MultisiteInterface::class) ||
            !$record->isMultisiteEnabled()
        ) {
            return [];
        }

        $page = Page::loadCached($theme, $pageCode);
        if (!$page) {
            return [];
        }

        $result = [];
        $otherRecords = $record->newOtherSiteQuery()->get();
        if (!$otherRecords || !$otherRecords->count()) {
            return [];
        }

        foreach (Site::listEnabled() as $site) {
            $otherRecord = $otherRecords->where('site_id', $site->id)->first();
            if (!$otherRecord) {
                continue;
            }

            $url = Cms::siteUrl($page, $site, [
                'id' => $otherRecord->id,
                'slug' => $otherRecord->slug,
                'fullslug' => $otherRecord->fullslug
            ]);

            $result[] = [
                'url' => $url,
                'id' => $site->id,
                'code' => $site->code,
                'locale' => $site->hard_locale,
            ];
        }

        return $result;
    }

    /**
     * pageManagerTypeToModel returns the resolved model and its scoped query for a given type
     */
    protected function pageManagerTypeToModel(string $typeName)
    {
        $model = $query = null;

        if (str_starts_with($typeName, 'list-')) {
            $typeName = substr($typeName, 5);
        }

        if (str_starts_with($typeName, 'entry-')) {
            $model = EntryRecord::inSectionUuid(substr($typeName, 6));
            $query = $model->applyVisibleFrontend();
        }

        return [$model, $query];
    }

    /**
     * pageManagerBlueprintToType
     */
    protected function pageManagerBlueprintToType($blueprint): string
    {
        if ($blueprint instanceof EntryBlueprint) {
            return 'entry-' . $blueprint->uuid;
        }

        return '';
    }
}
