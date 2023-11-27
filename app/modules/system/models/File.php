<?php namespace System\Models;

use Url;
use Config;
use Storage;
use Backend\Controllers\Files;
use October\Rain\Database\Attach\File as FileBase;

/**
 * File attachment model
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class File extends FileBase
{
    /**
     * @var string table in database used by the model
     */
    protected $table = 'system_files';

    /**
     * {@inheritDoc}
     */
    public function getThumbUrl($width, $height, $options = [])
    {
        if (!$this->isPublic() && class_exists(Files::class)) {
            $options = $this->getDefaultThumbOptions($options);

            // Ensure that the thumb exists first
            parent::getThumbUrl($width, $height, $options);

            // Return the Files controller handler for the URL
            return Files::getThumbUrl($this, $width, $height, $options);
        }

        return parent::getThumbUrl($width, $height, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function getPath($fileName = null)
    {
        if (!$this->isPublic() && class_exists(Files::class)) {
            return Files::getDownloadUrl($this);
        }

        return parent::getPath($fileName);
    }

    /**
     * getLocalRootPath will, if working with local storage, determine the absolute local path
     */
    protected function getLocalRootPath()
    {
        return Config::get('filesystems.disks.uploads.root', storage_path('app/uploads'));
    }

    /**
     * getPublicPath returns the public address for the storage path
     */
    public function getPublicPath()
    {
        $uploadsPath = Config::get('filesystems.disks.uploads.url', '/storage/app/uploads');

        if ($this->isPublic()) {
            $uploadsPath .= '/public';
        }
        else {
            $uploadsPath .= '/protected';
        }

        // Relative links
        if ($this->isLocalStorage() && Config::get('system.relative_links') === true) {
            return Url::toRelative($uploadsPath) . '/';
        }

        return Url::asset($uploadsPath) . '/';
    }

    /**
     * isLocalStorage returns true if storage.uploads.disk in config/system.php is "local"
     * @return bool
     */
    protected function isLocalStorage()
    {
        return Config::get('filesystems.disks.uploads.driver') == 'local';
    }

    /**
     * getDisk returns the storage disk the file is stored on
     * @return FilesystemAdapter
     */
    public function getDisk()
    {
        return Storage::disk('uploads');
    }
}
