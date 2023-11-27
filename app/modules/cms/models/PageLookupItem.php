<?php namespace Cms\Models;

use App;
use Url;
use Event;
use Model;
use Request;
use October\Contracts\Element\FormElement;
use Cms\Classes\Theme;

/**
 * PageLookupItem used by the pagefinder form widget
 *
 * @package october\cms
 * @author Alexey Bobkov, Samuel Georges
 */
class PageLookupItem extends Model
{
    /**
     * @var bool singleMode only allows items to be selected that resolve to a single URL.
     */
    public $singleMode = false;

    /**
     * @var bool nesting determines if auto-generated menu items could have subitems.
     */
    public $nesting = false;

    /**
     * @var array|bool sites includes a lookup for other sites.
     */
    public $sites = false;

    /**
     * @var array pageTypeInfoCache
     */
    protected $pageTypeInfoCache = [];

    /**
     * defineFormFields
     */
    public function defineFormFields(FormElement $host)
    {
        $host->addFormField('search')->displayAs('partial')->path('field_page_search');
        $host->addFormField('type', 'Type')->displayAs('dropdown')->span('row')->spanClass('col-4');
        $host->addFormField('url', 'URL')->dependsOn('type')->span('row')->spanClass('col-8');
        $host->addFormField('reference', 'Reference')->displayAs('dropdown')->dependsOn('type')->span('row')->spanClass('col-8');
        $host->addFormField('cms_page', 'CMS Page')->displayAs('dropdown')->dependsOn('type');
    }

    /**
     * filterFields used by the form controller
     */
    public function filterFields($fields)
    {
        if ($this->type === 'url') {
            $fields->reference->hidden();
            $fields->cms_page->hidden();
        }
        elseif ($this->type === 'cms-page') {
            $fields->url->hidden();
            $fields->cms_page->hidden();
        }
        else {
            $fields->url->hidden();
        }

        if (!$this->typeInfoHasAttribute('cmsPages')) {
            $fields->cms_page->hidden();
        }

        if (!$this->typeInfoHasAttribute('references')) {
            $fields->reference->disabled();
        }
    }

    /**
     * typeInfoHasAttribute
     */
    protected function typeInfoHasAttribute($attribute): bool
    {
        return array_key_exists($attribute, $this->getTypeInfo((string) $this->type));
    }

    /**
     * getCmsPageAttribute allows access to cms_page and cmsPage
     */
    public function getCmsPageAttribute()
    {
        return $this->attributes['cms_page'] ?? null;
    }

    /**
     * getTypeOptions
     */
    public function getTypeOptions()
    {
        $result = [
            'url' => 'URL',
        ];

        /**
         * @event cms.pageLookup.listTypes
         * Lists available types for locating CMS pages.
         *
         * Example usage:
         *
         *     Event::listen('cms.pageLookup.listTypes', function() {
         *         return [
         *             'blog-post' => 'Blog Post',
         *             'blog-category' => 'Blog Category',
         *             'blog-posts' => ['All Blog Posts', true],
         *         ];
         *     });
         *
         */
        $apiResult = Event::fire('cms.pageLookup.listTypes');

        if (is_array($apiResult)) {
            foreach ($apiResult as $typeList) {
                if (!is_array($typeList)) {
                    continue;
                }

                foreach ($typeList as $typeCode => $typeName) {
                    $isNested = false;

                    // If the last item in the array is a boolean, it defines nesting
                    if (
                        is_array($typeName) &&
                        count($typeName) > 1 &&
                        is_bool($typeName[array_key_last($typeName)])
                    ) {
                        $isNested = array_pop($typeName);
                        $typeName = array_shift($typeName);
                    }

                    if (!$typeName) {
                        continue;
                    }

                    if ($this->singleMode && $isNested) {
                        continue;
                    }

                    $result[$typeCode] = $typeName;
                }
            }
        }

        return $result;
    }

    /**
     * getTypeLabel
     */
    public function getTypeLabel()
    {
        return $this->getTypeOptions()[$this->type] ?? '';
    }

    /**
     * getReferenceOptions
     */
    public function getReferenceOptions()
    {
        return $this->buildReferenceOptions(
            $this->getTypeInfo((string) $this->type)['references'] ?? []
        );
    }

    /**
     * getReferenceLabel
     */
    public function getReferenceLabel()
    {
        $label = $this->getReferenceOptions()[$this->reference] ?? '';
        $label = str_replace('&nbsp;', '', $label);
        return $label;
    }

    /**
     * getCmsPageOptions
     */
    public function getCmsPageOptions()
    {
        return $this->getTypeInfo((string) $this->type)['cmsPages'] ?? [];
    }

    /**
     * buildReferenceOptions handles reference options where outcome can be a single
     * dimension array or an array with [title, items]
     */
    protected function buildReferenceOptions($references)
    {
        if (count($references) === count($references, COUNT_RECURSIVE)) {
            return $references;
        }

        $indent = '&nbsp;&nbsp;&nbsp;';
        $options = [];

        $iterator = function($items, $depth = 0) use (&$iterator, &$options, $indent) {
            foreach ($items as $code => $itemData) {
                if (is_array($itemData)) {
                    $options[$code] = str_repeat($indent, $depth) . ($itemData['title'] ?? '');
                    if (!empty($itemData['items'])) {
                        $iterator($itemData['items'], $depth + 1);
                    }
                }
                elseif (is_string($itemData)) {
                    $options[$code] = str_repeat($indent, $depth) . $itemData;
                }
            }

            return $options;
        };

        return $iterator($references);
    }

    /**
     * getTypeInfo
     */
    public function getTypeInfo(string $type): array
    {
        if (!$type) {
            return [];
        }

        if (array_key_exists($type, $this->pageTypeInfoCache)) {
            return $this->pageTypeInfoCache[$type];
        }

        if ($type === 'url') {
            $result = [];
        }
        else {
            $result = $this->getTypeInfoFromEvent($type);
        }

        return $this->pageTypeInfoCache[$type] = $result;
    }

    /**
     * getTypeInfoFromEvent
     */
    protected function getTypeInfoFromEvent(string $type): array
    {
        $result = [];
        $apiResult = Event::fire('cms.pageLookup.getTypeInfo', [$type]);

        if (!is_array($apiResult)) {
            return $result;
        }

        foreach ($apiResult as $typeInfo) {
            if (!is_array($typeInfo)) {
                continue;
            }

            foreach ($typeInfo as $name => $value) {
                // Convert Page object to key value pair
                if ($name === 'cmsPages') {
                    $cmsPages = [];

                    foreach ($value as $page) {
                        $baseName = $page->getBaseFileName();
                        $pos = strrpos($baseName, '/');

                        $dir = $pos !== false ? substr($baseName, 0, $pos).' / ' : null;
                        $cmsPages[$baseName] = strlen($page->title)
                            ? $dir . $page->title
                            : $baseName;
                    }

                    $value = $cmsPages;
                }

                $result[$name] = $value;
            }
        }

        return $result;
    }

    /**
     * resolveItem
     */
    public function resolveItem()
    {
        if ($this->type === 'url') {
            return $this;
        }

        $currentUrl = mb_strtolower(Url::to(Request::path()));
        $defaultTheme = App::runningInBackend()
            ? Theme::getEditTheme()
            : Theme::getActiveTheme();

        $apiResult = Event::fire('cms.pageLookup.resolveItem', [
            $this->type,
            $this,
            $currentUrl,
            $defaultTheme
        ]);

        if (!is_array($apiResult)) {
            return $this;
        }

        foreach ($apiResult as $itemInfo) {
            if (!is_array($itemInfo)) {
                continue;
            }

            $this->title = $itemInfo['title'] ?? $this->title;
            $this->url = $itemInfo['url'] ?? $this->url;
            $this->isActive = $itemInfo['isActive'] ?? false;
            $this->viewBag = $itemInfo['viewBag'] ?? [];
            $this->code = $itemInfo['code'] ?? null;
            $this->mtime = $itemInfo['mtime'] ?? null;
            $this->sites = $itemInfo['sites'] ?? null;

            $this->attributes = array_merge($this->attributes, $itemInfo);

            if (isset($itemInfo['items']) && is_array($itemInfo['items'])) {
                $this->items = $this->buildChildItems($itemInfo['items']);
            }
        }

        return $this;
    }

    /**
     * buildChildItems
     */
    protected function buildChildItems($items)
    {
        $result = [];

        foreach ($items as $item) {
            $reference = new static;
            $reference->type = $item['type'] ?? null;
            $reference->title = $item['title'] ?? '-- no title --';
            $reference->url = $item['url'] ?? '#';
            $reference->isActive = $item['isActive'] ?? false;
            $reference->viewBag = $item['viewBag'] ?? [];
            $reference->code = $item['code'] ?? null;
            $reference->mtime = $item['mtime'] ?? null;
            $reference->sites = $item['sites'] ?? null;

            if (isset($item['items'])) {
                $reference->items = $this->buildChildItems($item['items']);
            }

            $result[] = $reference;
        }

        return $result;
    }

    /**
     * resolveFromSchema
     */
    public static function resolveFromSchema(string $address, array $options = []): ?PageLookupItem
    {
        $item = static::fromSchema($address);
        if (!$item) {
            return null;
        }

        $item->nesting = (bool) array_get($options, 'nesting', false);
        $item->sites = (bool) array_get($options, 'sites', false);

        return $item->resolveItem();
    }

    /**
     * fromSchema
     */
    public static function fromSchema(string $address): ?PageLookupItem
    {
        $decoded = self::decodeSchema($address);
        if (!$decoded) {
            return null;
        }

        $item = new static;
        $item->type = $decoded['type'];

        if ($item->type === 'url') {
            $item->url = $decoded['url'] ?? '';
        }
        else {
            $item->reference = $decoded['reference'];
            $item->attributes = array_merge($decoded['params'], $item->attributes);
        }

        return $item;
    }

    /**
     * decodeSchema will decode an October CMS protocol, e.g.
     * `october://cms-page/about/home/index?target=_blank`
     */
    public static function decodeSchema(string $address): array
    {
        $parts = parse_url($address);
        $schema = $parts['scheme'] ?? null;
        $target = $parts['host'] ?? null;
        $type = $parts['user'] ?? '';
        parse_str($parts['query'] ?? '', $params);

        if ($schema === 'october' && $target === 'link') {
            return [
                'type' => $type,
                'reference' => ltrim($parts['path'] ?? '', '/'),
                'params' => $params,
            ];
        }

        if (self::isValidUrl($address)) {
            return [
                'type' => 'url',
                'url' => $address,
            ];
        }

        return [];
    }

    /**
     * isValidUrl determines if the given path is a valid URL, similar to Laravel's
     * except it accepts relative paths. Eg: /contact
     * @param  string  $path
     * @return bool
     */
    public static function isValidUrl($path)
    {
        if (!preg_match('~^(#|/|https?://|(mailto|tel|sms):)~', $path)) {
            return filter_var($path, FILTER_VALIDATE_URL) !== false;
        }

        return true;
    }

    /**
     * encodeSchema will encode an October CMS protocol, e.g.
     * `('cms-page', 'about/home/index', ['target' => '_blank'])`
     */
    public static function encodeSchema(string $type, string $reference = '', array $params = []): string
    {
        if ($type === 'url') {
            return $params['url'] ?? '';
        }

        $query = is_array($params) && $params ? '?' . http_build_query($params) : '';

        return "october://{$type}@link/{$reference}{$query}";
    }
}
