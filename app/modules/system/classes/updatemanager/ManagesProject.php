<?php namespace System\Classes\UpdateManager;

use File;
use Cache;
use System as SystemHelper;
use System\Models\Parameter;
use October\Rain\Composer\Manager as ComposerManager;

/**
 * ManagesProject
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
trait ManagesProject
{
    /**
     * canUpdateProject checks if composer is ready to access the gateway using authentication
     */
    public function canUpdateProject(): bool
    {
        return $this->requestProjectDetails($this->getComposerProjectKey())['is_active'] ?? false;
    }

    /**
     * getComposerProjectKey returns the project key used by composer
     */
    public function getComposerProjectKey(): ?string
    {
        return ComposerManager::instance()
            ->getAuthCredentials($this->getComposerUrl(false))['password'] ?? null;
    }

    /**
     * getProjectKey locates the project key from the file system and seeds the parameter
     */
    public function getProjectKey()
    {
        if (
            File::exists($seedFile = storage_path('cms/project.json')) &&
            ($contents = json_decode(File::get($seedFile), true)) &&
            isset($contents['project'])
        ) {
            Parameter::set('system::project.key', $contents['project']);
            File::delete($seedFile);
        }

        return Parameter::get('system::project.key');
    }

    /**
     * getProjectDetails returns the active project details
     */
    public function getProjectDetails(): ?object
    {
        if (!$projectKey = $this->getProjectKey()) {
            return null;
        }

        $projectId = Parameter::get('system::project.id');

        if (!$projectId) {
            $details = $this->requestProjectDetails($projectKey);
            if (!isset($details['id'])) {
                return null;
            }

            Parameter::set([
                'system::project.id' => $details['id'],
                'system::project.key' => $details['project_id'],
                'system::project.name' => $details['name'],
                'system::project.owner' => $details['owner'],
                'system::project.is_active' => $details['is_active']
            ]);
        }

        return (object) [
            'id' => $projectId,
            'key' => $projectKey,
            'name' => Parameter::get('system::project.name'),
            'owner' => Parameter::get('system::project.owner'),
            'is_active' => Parameter::get('system::project.is_active'),
        ];
    }

    /**
     * storeProjectDetails
     */
    public function storeProjectDetails(array $details)
    {
        // Save project locally
        Parameter::set([
            'system::project.id' => $details['id'],
            'system::project.key' => $details['project_id'],
            'system::project.name' => $details['name'],
            'system::project.owner' => $details['owner'],
            'system::project.is_active' => $details['is_active']
        ]);

        // Save authentication token
        ComposerManager::instance()->addAuthCredentials(
            $this->getComposerUrl(false),
            $details['email'],
            $details['project_id']
        );
    }

    /**
     * syncProjectPackages compares installed packages to project packages
     */
    public function syncProjectPackages(): array
    {
        $crossCheckPackage = function(string $composerCode, array $packages): bool {
            foreach ($packages as $package) {
                $name = $package['name'] ?? null;
                if ($name === $composerCode) {
                    return true;
                }
            }

            return false;
        };

        $plugins = $themes = [];
        $packages = ComposerManager::instance()->listAllPackages();
        $project = $this->requestProjectDetails();

        foreach (($project['plugins'] ?? []) as $plugin) {
            $toCode = $plugin['code'] ?? null;
            $composerCode = $plugin['composer_code'] ?? null;
            $composerVersion = $plugin['composer_version'] ?? '*';

            if ($composerCode === null || $crossCheckPackage($composerCode, $packages)) {
                continue;
            }

            $plugins[$toCode] = [$composerCode, $composerVersion];
        }

        foreach (($project['themes'] ?? []) as $theme) {
            $toCode = $theme['code'] ?? null;
            $composerCode = $theme['composer_code'] ?? null;
            $composerVersion = $theme['composer_version'] ?? '*';

            if ($composerCode === null || $crossCheckPackage($composerCode, $packages)) {
                continue;
            }

            $themes[$toCode] = [$composerCode, $composerVersion];
        }

        return array_merge($plugins, $themes);
    }

    /**
     * requestProjectDetails requests details about a project based on its identifier
     */
    public function requestProjectDetails(string $projectKey = null): array
    {
        if ($projectKey === null) {
            $projectKey = $this->getProjectKey();
        }

        return $this->requestServerData('project/detail', ['id' => $projectKey]);
    }

    /**
     * requestBrowseProject will list project details and cache it
     */
    public function requestBrowseProject()
    {
        $cacheKey = 'system-market-project';

        if (Cache::has($cacheKey)) {
            return @json_decode(@base64_decode(Cache::get($cacheKey)), true) ?: [];
        }

        $data = $this->requestProjectDetails();

        // 5 minutes
        $expiresAt = now()->addMinutes(5);
        Cache::put($cacheKey, base64_encode(json_encode($data)), $expiresAt);

        return $data;
    }

    /**
     * requestBrowseProducts will list available products
     */
    public function requestBrowseProducts($type = null, $page = null)
    {
        if ($type !== 'plugin' && $type !== 'theme') {
            $type = 'plugin';
        }

        $cacheKey = "system-market-browse-{$type}-{$page}";

        if (Cache::has($cacheKey)) {
            return @json_decode(@base64_decode(Cache::get($cacheKey)), true) ?: [];
        }

        $data = $this->requestServerData('package/browse', [
            'type' => $type,
            'page' => $page,
            'version' => SystemHelper::VERSION
        ]);

        // 60 minutes
        $expiresAt = now()->addMinutes(60);
        Cache::put($cacheKey, base64_encode(json_encode($data)), $expiresAt);

        return $data;
    }
}
