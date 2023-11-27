<?php namespace System\Classes;

use Url;
use Log;
use App;
use File;
use Lang;
use Route;
use Event;
use Cache;
use Config;
use Storage;
use Redirect;
use Exception;
use October\Rain\Database\Attach\File as FileModel;
use ApplicationException;
use Resizer;

/**
 * ResizeImages is used for resizing image files
 *
 * @method static ResizeImages instance()
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class ResizeImages
{
    use \October\Rain\Support\Traits\Singleton;

    /**
     * @var array availableSources to get image paths
     */
    protected $availableSources = [];

    /**
     * @var string storageFolder is the name of the folder in the resources disk
     */
    protected $storageFolder = 'resize';

    /**
     * @var string storageUrl relative or absolute URL of the Library root folder.
     */
    protected $storageUrl;

    /**
     * init is a singleton constructor
     */
    public function init()
    {
        $this->storageUrl = rtrim(Config::get('filesystems.disks.resources.url', '/storage/app/resources'), '/');
    }

    /**
     * resize
     */
    public static function resize($image, $width = null, $height = null, $options = [])
    {
        return self::instance()->prepareRequest($image, $width, $height, $options);
    }

    /**
     * getContents
     */
    public function getContents($cacheKey)
    {
        $cacheInfo = $this->getCache($cacheKey);
        if (!$cacheInfo || !isset($cacheInfo['path'])) {
            throw new ApplicationException(Lang::get('system::lang.resizer.not_found', ['name'=>$cacheKey]));
        }

        // Calculate properties
        $width = $cacheInfo['width'] ?? 0;
        $height = $cacheInfo['height'] ?? 0;
        $options = (array) @json_decode(@base64_decode($cacheInfo['options']), true);
        $filename = $this->getResizeFilename($cacheKey, $width, $height, $options);

        // Set local paths for resizer
        $tempTargetPath = $this->getTempPath() . '/' . $filename;
        $tempSourcePath = $this->getTempPath() . '/raw_' . $filename;
        $sourcePath = $this->getSourcePathForResize($cacheInfo['path'], $tempSourcePath);

        // Perform resize
        Resizer::open($sourcePath)
            ->resize($width, $height, $options)
            ->save($tempTargetPath);

        // Save resized file to disk
        $disk = Storage::disk('resources');
        $filePath = $this->storageFolder . '/' . $filename;
        $success = $disk->putFileAs(
            dirname($filePath),
            $tempTargetPath,
            basename($filePath)
        );

        // Clean up
        File::delete($tempTargetPath);

        if (file_exists($tempSourcePath)) {
            File::delete($tempSourcePath);
        }

        // Eagerly cache remote exists call
        if ($success && !$this->isLocalStorage()) {
            Cache::forever($this->getExistsCacheKey($filePath), true);
        }

        return Redirect::to($this->getPublicPath() . '/' . $filename);
    }

    /**
     * getSourcePathForResize creates a temp copy of external files in the local filesystem
     */
    protected function getSourcePathForResize($realSourcePath, $tempSourcePath)
    {
        $isExternal = strpos($realSourcePath, 'http') === 0;
        $sourcePath = $isExternal ? $tempSourcePath : $realSourcePath;

        if ($isExternal) {
            try {
                $contents = file_get_contents($realSourcePath);
                file_put_contents($tempSourcePath, $contents);
            }
            catch (Exception $ex) {
                Log::warning('Unable to fetch external image ' . $realSourcePath . ' ['.$ex->getMessage().']');
            }
        }

        if (!file_exists($sourcePath)) {
            /**
             * @event system.resizer.handleMissingImage
             * Provides an opportunity to configure a custom image when the resizer couldn't find the original file
             *
             * Example usage:
             *
             *     Event::listen('system.resizer.handleMissingImage', function(&$sourcePath) {
             *         $sourcePath = plugins_path('vendor/plugin/assets/broken-image.jpg');
             *     });
             *
             */
            Event::fire('system.resizer.handleMissingImage', [&$sourcePath]);
        }

        return $sourcePath;
    }

    /**
     * prepareRequest for resizing
     */
    protected function prepareRequest($image, $width = null, $height = null, $options = [])
    {
        $imageInfo = $this->processImage($image);
        $options = $this->getDefaultResizeOptions($options);

        // SVGs are sent back raw
        if (strtolower($imageInfo['extension']) === 'svg') {
            return $imageInfo['url'];
        }

        // Use the same extension as source image
        if ($options['extension'] === 'auto') {
            $options['extension'] = $imageInfo['extension'];
        }

        $width = (int) $width;
        $height = (int) $height;

        // Check is resized
        $cacheKey = $this->getCacheKey([$imageInfo, $width, $height, $options]);
        $filename = $this->getResizeFilename($cacheKey, $width, $height, $options);

        if ($this->hasFile($this->storageFolder . '/' . $filename)) {
            return $this->getPublicPath() . '/' . $filename;
        }

        // Cache and process
        $cacheInfo = $this->getCache($cacheKey);

        if (!$cacheInfo) {
            $cacheInfo = [
                'version' => $cacheKey . '-1',
                'source' => $imageInfo['source'],
                'path' => $imageInfo['path'],
                'extension' => $imageInfo['extension'],
                'width' => $width,
                'height' => $height,
                'options' => base64_encode(json_encode($options)),
            ];

            $this->putCache($cacheKey, $cacheInfo);
        }

        $outputFilename = $cacheInfo['version'];

        return $this->getResizedUrl($outputFilename);
    }

    /**
     * hasFile checks file exists on storage device
     */
    protected function hasFile($filePath = null): bool
    {
        $disk = Storage::disk('resources');
        if ($this->isLocalStorage()) {
            return $disk->exists($filePath);
        }

        // Cache remote storage results for performance increase
        $result = Cache::rememberForever($this->getExistsCacheKey($filePath), function() use ($disk, $filePath) {
            return $disk->exists($filePath);
        });

        return $result;
    }

    /**
     * getResizedUrl
     */
    protected function getResizedUrl($outputFilename = 'undefined.css')
    {
        $combineAction = \System\Classes\SystemController::class.'@resize';
        $actionExists = Route::getRoutes()->getByAction($combineAction) !== null;

        if ($actionExists) {
            return Url::action($combineAction, [$outputFilename], false);
        }

        return '/resize/'.$outputFilename;
    }

    /**
     * processImage
     */
    protected function processImage($image)
    {
        $result = [
            'url' => null,
            'path' => null,
            'extension' => null,
            'source' => null
        ];

        // File model
        if ($image instanceof FileModel) {
            $disk = $image->getDisk();
            $path = $image->getDiskPath();

            if (File::extension($path) && $disk->exists($path)) {
                $result['url'] = $image->getPath();
                $result['path'] = $image->getLocalPath();
                $result['extension'] = $image->getExtension();
                $result['source'] = 'model';
            }
        }
        elseif (is_string($image)) {
            $path = $this->parseFileName($image);

            // Local path
            if ($path !== null) {
                $url = Config::get('system.relative_links') === true
                    ? Url::toRelative(File::localToPublic($path))
                    : Url::asset(File::localToPublic($path));

                $result['url'] = $url;
                $result['path'] = $path;
                $result['extension'] = File::extension($path);
                $result['source'] = 'local';
            }
            // URL
            elseif (strpos($image, '://') !== false) {
                $result['url'] = $result['path'] = $image;
                $result['extension'] = explode('?', File::extension($image))[0];
                $result['source'] = 'url';
            }
        }

        return $result;
    }

    /**
     * parseFileName to get a relative path for the file
     * @return string
     */
    protected function parseFileName($filePath): ?string
    {
        // Local disk path
        if (file_exists($filePath)) {
            return $filePath;
        }

        // Pop off URI from URL
        $path = urldecode(parse_url($filePath, PHP_URL_PATH));

        foreach ($this->getAvailableSources() as $source) {
            if ($source['disk'] !== 'local') {
                continue;
            }

            $rootPath = $source['root'] ?? '';
            $relativeUrl = Url::toRelative($source['url'] ?? '');

            if (!$rootPath || !$relativeUrl) {
                continue;
            }

            if (strpos($path, $relativeUrl) === false) {
                continue;
            }

            $pathParts = explode($relativeUrl, $path, 2);
            $finalPath = $rootPath . end($pathParts);
            if (file_exists($finalPath)) {
                return $finalPath;
            }
        }

        return null;
    }

    /**
     * getAvailableSources returns available sources
     */
    protected function getAvailableSources(): array
    {
        if ($this->availableSources) {
            return $this->availableSources;
        }

        $config = App::make('config');

        $sources = [
            'media' => [
                'disk' => $config->get('filesystems.disks.media.driver', 'local'),
                'root' => $config->get('filesystems.disks.media.root', storage_path('app/media')),
                'url' => $config->get('filesystems.disks.media.url', '/storage/app/media')
            ],
            'uploads' => [
                'disk' => $config->get('filesystems.disks.uploads.driver', 'local'),
                'root' => $config->get('filesystems.disks.uploads.root', storage_path('app/uploads')),
                'url' => $config->get('filesystems.disks.uploads.url', '/storage/app/uploads')
            ],
            'app' => [
                'disk' => 'local',
                'root' => base_path('app'),
                'url' => '/app'
            ],
            'modules' => [
                'disk' => 'local',
                'root' => base_path('modules'),
                'url' => '/modules'
            ],
            'plugins' => [
                'disk' => 'local',
                'root' => base_path('plugins'),
                'url' => '/plugins'
            ],
            'themes' => [
                'disk' => 'local',
                'root' => base_path('themes'),
                'url' => '/themes'
            ],
        ];

        /**
         * @event system.resizer.getAvailableSources
         * Provides an opportunity to modify the available sources
         *
         * Example usage:
         *
         *     Event::listen('system.resizer.getAvailableSources', function((array) &$sources)) {
         *         $sources['custom'] = [
         *              'disk' => 'custom',
         *              'root' => 'relative/path/on/disk',
         *              'url' => 'publicly/accessible/path',
         *         ];
         *     });
         *
         */
        Event::fire('system.resizer.getAvailableSources', [&$sources]);

        return $this->availableSources = $sources;
    }

    /**
     * getResizeFilename generates a thumbnail filename
     */
    protected function getResizeFilename($id, $width, $height, $options): string
    {
        $options = $this->getDefaultResizeOptions($options);
        $offsetA = $options['offset'][0];
        $offsetB = $options['offset'][1];
        $mode = $options['mode'];
        $extension = $options['extension'];

        return "img_{$id}_{$width}_{$height}_{$offsetA}_{$offsetB}_{$mode}.{$extension}";
    }

    /**
     * getDefaultResizeOptions returns the default thumbnail options
     */
    protected function getDefaultResizeOptions($overrideOptions = []): array
    {
        $defaultOptions = [
            'mode' => 'auto',
            'offset' => [0, 0],
            'quality' => 90,
            'sharpen' => 0,
            'interlace' => false,
            'extension' => 'auto',
        ];

        if (!is_array($overrideOptions)) {
            $overrideOptions = ['mode' => $overrideOptions];
        }

        $options = array_merge($defaultOptions, $overrideOptions);

        $options['mode'] = strtolower($options['mode']);

        return $options;
    }

    //
    // Paths
    //

    /**
     * getPublicPath returns the public address for the resources path
     */
    public function getPublicPath()
    {
        $publicPath = $this->storageUrl . '/resize';

        if ($this->isLocalStorage() && Config::get('system.relative_links') === true) {
            return Url::toRelative($publicPath);
        }

        return Url::asset($publicPath);
    }

    /**
     * getOutputPath returns the final resource path
     */
    public function getOutputPath()
    {
        $path = rtrim(Config::get('filesystems.disks.resources.root', storage_path('app/resources')), '/');
        $path .= '/'. $this->storageFolder;

        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true, true);
        }

        return $path;
    }

    /**
     * getTempPath returns an internal working path
     */
    public function getTempPath()
    {
        $path = temp_path('resize');

        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true, true);
        }

        return $path;
    }

    /**
     * isLocalStorage returns true if the storage engine is local
     */
    protected function isLocalStorage()
    {
        return Config::get('filesystems.disks.resources.driver') === 'local';
    }

    //
    // Cache
    //

    /**
     * Stores information about a asset collection against
     * a cache identifier.
     * @param string $cacheKey Cache identifier.
     * @param array $cacheInfo List of asset files.
     * @return bool Successful
     */
    protected function putCache($cacheKey, array $cacheInfo)
    {
        $cacheKey = 'resizer.'.$cacheKey;

        if (Cache::has($cacheKey)) {
            return false;
        }

        $this->putCacheIndex($cacheKey);

        Cache::forever($cacheKey, base64_encode(serialize($cacheInfo)));

        return true;
    }

    /**
     * Look up information about a cache identifier.
     * @param string $cacheKey Cache identifier
     * @return array Cache information
     */
    protected function getCache($cacheKey)
    {
        $cacheKey = 'resizer.'.$cacheKey;

        if (!Cache::has($cacheKey)) {
            return false;
        }

        return @unserialize(@base64_decode(Cache::get($cacheKey)));
    }

    /**
     * getCacheKey builds a unique string based on assets
     */
    protected function getCacheKey(array $payload): string
    {
        $cacheKey = json_encode($payload);

        /**
         * @event cms.resizer.getCacheKey
         * Provides an opportunity to modify the asset resizer's cache key
         *
         * Example usage:
         *
         *     Event::listen('cms.resizer.getCacheKey', function((\System\Classes\ResizeImages) $assetCombiner, (stdClass) $dataHolder) {
         *         $dataHolder->key = rand();
         *     });
         *
         */
        $dataHolder = (object) ['key' => $cacheKey];
        Event::fire('cms.resizer.getCacheKey', [$this, $dataHolder]);
        $cacheKey = $dataHolder->key;

        return md5($cacheKey);
    }

    /**
     * getExistsCacheKey builds a key for caching the exists check
     */
    protected function getExistsCacheKey(string $filePath): string
    {
        return $this->getCacheKey(['type' => 'resizer-file', 'path' => $filePath]);
    }

    /**
     * Resets the resizer cache
     * @return void
     */
    public static function resetCache()
    {
        if (Cache::has('resizer.index')) {
            $index = (array) @unserialize(@base64_decode(Cache::get('resizer.index'))) ?: [];

            foreach ($index as $cacheKey) {
                Cache::forget($cacheKey);
            }

            Cache::forget('resizer.index');
        }

        // CacheHelper::instance()->clearCombiner();
    }

    /**
     * Adds a cache identifier to the index store used for
     * performing a reset of the cache.
     * @param string $cacheKey Cache identifier
     * @return bool Returns false if identifier is already in store
     */
    protected function putCacheIndex($cacheKey)
    {
        $index = [];

        if (Cache::has('resizer.index')) {
            $index = (array) @unserialize(@base64_decode(Cache::get('resizer.index'))) ?: [];
        }

        if (in_array($cacheKey, $index)) {
            return false;
        }

        $index[] = $cacheKey;

        Cache::forever('resizer.index', base64_encode(serialize($index)));

        return true;
    }
}
