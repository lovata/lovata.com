<?php namespace Backend\Models;

use Lang;
use Model;
use Cache;
use SystemException;
use ApplicationException;

/**
 * Dashboard definition
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $definition
 * @property string $icon
 * @property bool $global_access
 * @property int $updated_user_id
 * @property int $created_user_id
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $created_at
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class Dashboard extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\UserFootprints;

    /**
     * @var string PERMISSION_CACHE_KEY for the dashboard lookup
     */
    const PERMISSION_CACHE_KEY = 'backend.dashboard_permission_cache';

    /**
     * @var string table associated with the model
     */
    protected $table = 'backend_dashboards';

    /**
     * @var array rules for validation
     */
    public $rules = [
        'name' => 'required',
        'slug' => 'required|unique:backend_dashboards',
        'definition' => 'required'
    ];

    /**
     * afterSave model event
     */
    public function afterSave()
    {
        $this->clearCache();
    }

    /**
     * Retrieves a list of dashboards accessible to a given user.
     *
     * @param User $user The user for whom to list the dashboards.
     * @return Dashboard[] Returns an array of dashboard objects.
     */
    public static function listUserDashboards(User $user): array
    {
        $dashboards = self::orderBy('name')->get();
        $isSuperUser = self::userHasEditDashboardPermissions($user);
        $result = [];
        foreach ($dashboards as $dashboard) {
            if (
                !$isSuperUser &&
                !$user->hasPermission("dashboard.access_dashboard_{$dashboard->id}") &&
                !$dashboard->global_access
            ) {
                continue;
            }

            $result[] = $dashboard;
        }

        return $result;
    }

    /**
     * getPermissionDefinitions returns cached permission definitions for each dashboard,
     * suitable for the registerPermissions plugin registration method.
     */
    public static function getPermissionDefinitions(): array
    {
        return Cache::remember(self::PERMISSION_CACHE_KEY, 1440, function() {
            $dashboards = self::select('id', 'name')
                ->orderBy('name')
                ->get()
            ;

            $result = [];
            foreach ($dashboards as $index => $dashboard) {
                $result["dashboard.access_dashboard_{$dashboard->id}"] = [
                    'label' => __("Access :name Dashboard", ['name' => $dashboard->name]),
                    'tab' => 'Dashboard',
                    'order' => 600 + $index
                ];
            }
            return $result;
        });
    }

    /**
     * userHasEditDashboardPermissions
     */
    public static function userHasEditDashboardPermissions(User $user)
    {
        return $user->isSuperUser() ||
            $user->hasPermission('dashboard.manage');
    }

    /**
     * updateDashboard
     */
    public static function updateDashboard(string $slug, string $definition)
    {
        $slug = strtolower($slug);
        if (!strlen($slug)) {
            throw new SystemException('Slug must not be empty');
        }

        if (!strlen($definition)) {
            throw new SystemException('Dashboard definition must not be empty');
        }

        $decoded = json_decode($definition);
        if (!is_array($decoded)) {
            throw new SystemException('Invalid dashboard data');
        }

        $dashboard = self::where('slug', $slug)->first();
        if (!$dashboard) {
            throw new ApplicationException(
                Lang::get('backend::lang.dashboard.not_found_by_slug', ['slug' => $slug])
            );
        }

        $dashboard->definition = $definition;
        $dashboard->save();
    }

    /**
     * createDashboard
     */
    public static function createDashboard(string $name, string $slug, string $icon, ?int $userId, bool $globalAccess, ?string $definition = null)
    {
        $slug = strtolower($slug);
        self::validateSlug($slug);
        if (self::where('slug', $slug)->first()) {
            throw new ApplicationException(
                Lang::get('backend::lang.dashboard.slug_already_exists', ['slug' => $slug])
            );
        }

        $dashboard = new Dashboard();
        $dashboard->name = $name;
        $dashboard->slug = $slug;
        $dashboard->icon = $icon;
        $dashboard->definition = $definition ?? '[{"widgets":[]}]';
        $dashboard->created_user_id = $userId;
        $dashboard->global_access = $globalAccess;
        $dashboard->validate();
        $dashboard->save();
    }

    public static function updateDashboardConfig(string $originalSlug, string $name, string $slug, string $icon, bool $globalAccess)
    {
        $slug = strtolower($slug);
        $originalSlug = strtolower($originalSlug);

        $dashboard = self::where('slug', $originalSlug)->first();
        if (!$dashboard) {
            throw new ApplicationException(
                Lang::get('backend::lang.dashboard.not_found_by_slug', ['slug' => $originalSlug])
            );
        }

        if (self::where('slug', $slug)->where('id', '<>', $dashboard->id)->first()) {
            throw new ApplicationException(
                Lang::get('backend::lang.dashboard.slug_already_exists', ['slug' => $slug])
            );
        }

        self::validateSlug($slug);

        $dashboard->name = $name;
        $dashboard->slug = $slug;
        $dashboard->icon = $icon;
        $dashboard->global_access = $globalAccess;
        $dashboard->validate();
        $dashboard->save();
    }

    public static function deleteDashboard(string $slug)
    {
        $slug = strtolower($slug);
        $dashboard = self::where('slug', $slug)->first();
        if (!$dashboard) {
            throw new ApplicationException(
                Lang::get('backend::lang.dashboard.not_found_by_slug', ['slug' => $slug])
            );
        }

        $dashboard->delete();
    }

    public static function getConfigurationAsJson(string $slug)
    {
        $slug = strtolower($slug);
        $dashboard = self::where('slug', $slug)->first();
        if (!$dashboard) {
            throw new ApplicationException(
                Lang::get('backend::lang.dashboard.not_found_by_slug', ['slug' => $slug])
            );
        }

        $result = [
            'definition' => $dashboard->definition,
            'slug' => $dashboard->slug,
            'icon' => $dashboard->icon,
            'name' => $dashboard->name,
            'schema' => 1
        ];

        return json_encode($result, JSON_PRETTY_PRINT);
    }

    public function getStateProps()
    {
        $result = [
            "slug" => $this->slug,
            "name" => $this->name,
            "icon" => $this->icon,
            "global_access" => $this->global_access,
            "rows" => json_decode($this->definition)
        ];

        return $result;
    }

    public static function import(string $content, ?int $userId, bool $globalAccess = false): string
    {
        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            throw new ApplicationException("Error decoding the uploaded file");
        }

        $requiredProps = [
            'definition',
            'slug',
            'icon',
            'name',
            'schema'
        ];

        foreach ($requiredProps as $property) {
            if (!array_key_exists($property, $decoded)) {
                throw new ApplicationException("The uploaded file is not a valid dashboard definition [1]");
            }

            $value = $decoded[$property];
            if (!strlen($value) || !is_scalar($value)) {
                throw new ApplicationException("The uploaded file is not a valid dashboard definition [2]");
            }
        }

        $decodedDefinition = json_decode($decoded['definition']);
        if (!is_array($decodedDefinition)) {
            throw new ApplicationException('Invalid dashboard data');
        }

        $slug = strtolower($decoded['slug']);
        self::validateSlug($slug);

        $slug = self::uniqueSlug($slug);

        self::createDashboard(
            $decoded['name'],
            $slug,
            $decoded['icon'],
            $userId,
            $globalAccess,
            $decoded['definition']
        );

        return $slug;
    }

    private static function uniqueSlug(string $slug): string
    {
        $slug = strtolower($slug);
        $originalSlug = $slug;
        $index = 1;
        while (self::where('slug', $slug)->first()) {
            $index++;
            $slug = $originalSlug . '-' . $index;
        }

        return $slug;
    }

    private static function validateSlug($slug)
    {
        if (!preg_match('/^[0-9a-zA-Z\-]+$/', $slug)) {
            throw new ApplicationException('Invalid slug');
        }
    }

    /**
     * clearCache
     */
    public function clearCache()
    {
        Cache::forget(self::PERMISSION_CACHE_KEY);
    }
}
