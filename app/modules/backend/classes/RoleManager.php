<?php namespace Backend\Classes;

use App;
use System;
use System\Classes\PluginManager;
use October\Rain\Exception\SystemException;

/**
 * RoleManager manages the backend roles and permissions.
 *
 * @method static RoleManager instance()
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class RoleManager
{
    use \October\Rain\Support\Traits\Singleton;

    /**
     * @var array callbacks for registration.
     */
    protected $callbacks = [];

    /**
     * @var array permissions registered.
     */
    protected $permissions = [];

    /**
     * @var array permissionRoles is a list of registered permission roles.
     */
    protected $permissionRoles = false;

    /**
     * @var array permissionCache of registered permissions.
     */
    protected $permissionCache = false;

    /**
     * registerCallback registers a callback function that defines authentication permissions.
     * The callback function should register permissions by calling the manager's
     * registerPermissions() function. The manager instance is passed to the
     * callback function as an argument. Usage:
     *
     *     RoleManager::registerCallback(function ($manager) {
     *         $manager->registerPermissions([...]);
     *     });
     *
     * @param callable $callback A callable function.
     */
    public function registerCallback(callable $callback)
    {
        $this->callbacks[] = $callback;
    }

    /**
     * registerPermissions registers the back-end permission items.
     * The argument is an array of the permissions. The array keys represent the
     * permission codes, specific for the plugin/module. Each element in the
     * array should be an associative array with the following keys:
     * - label - specifies the menu label localization string key, required.
     * - order - a position of the item in the menu, optional.
     * - comment - a brief comment that describes the permission, optional.
     * - tab - assign this permission to a tabbed group, optional.
     * @param string $owner Specifies the permissions' owner plugin or module in the format Author.Plugin
     * @param array $definitions An array of the menu item definitions.
     */
    public function registerPermissions($owner, array $definitions)
    {
        foreach ($definitions as $code => $definition) {
            $permission = new RolePermission(array_merge($definition, [
                'code' => $code,
                'owner' => $owner
            ]));

            $this->permissions[] = $permission;
        }
    }

    /**
     * removePermission removes a single back-end permission. Where owner specifies the
     * permissions' owner plugin or module in the format Author.Plugin. Where code is
     * the permission to remove.
     */
    public function removePermission(string $owner, string $code)
    {
        if (!$this->permissions) {
            throw new SystemException('Unable to remove permissions before they are loaded.');
        }

        $ownerPermissions = array_filter($this->permissions, function ($permission) use ($owner) {
            return $permission->owner === $owner;
        });

        foreach ($ownerPermissions as $key => $permission) {
            if ($permission->code === $code) {
                unset($this->permissions[$key]);
            }
        }
        
        $this->permissionCache = $this->permissions;
    }

    /**
     * listPermissions returns a list of the registered permissions items.
     */
    public function listPermissions(): array
    {
        if ($this->permissionCache !== false) {
            return $this->permissionCache;
        }

        // Load external items
        foreach ($this->callbacks as $callback) {
            $callback($this);
        }

        // Load module items
        foreach (System::listModules() as $module) {
            if ($provider = App::getProvider($module . '\\ServiceProvider')) {
                $items = $provider->registerPermissions();
                if (is_array($items)) {
                    $this->registerPermissions('October.'.$module, $items);
                }
            }
        }

        // Load plugin items
        foreach (PluginManager::instance()->getPlugins() as $id => $plugin) {
            $items = $plugin->registerPermissions();
            if (is_array($items)) {
                $this->registerPermissions($id, $items);
            }
        }

        // Load app items
        if ($app = App::getProvider(\App\Provider::class)) {
            $items = $app->registerPermissions();
            if (is_array($items)) {
                $this->registerPermissions('October.App', $items);
            }
        }

        // Sort permission items
        usort($this->permissions, function ($a, $b) {
            if ($a->order === $b->order) {
                return 0;
            }

            return $a->order > $b->order ? 1 : -1;
        });

        return $this->permissionCache = $this->permissions;
    }

    /**
     * listPermissionsForUser returns permissions that the user has access to.
     */
    public function listPermissionsForUser($user): array
    {
        return array_filter($this->listPermissions(), function($permission) use ($user) {
            return $user->hasAccess($permission->code);
        });
    }

    /**
     * listPermissionsForRole returns an array of registered permissions belonging to a
     * given role code.
     * @param string $role
     * @param bool $includeOrphans
     */
    public function listPermissionsForRole($role, $includeOrphans = true): array
    {
        if ($this->permissionRoles === false) {
            $this->permissionRoles = [];

            foreach ($this->listPermissions() as $permission) {
                if ($permission->roles) {
                    foreach ((array) $permission->roles as $_role) {
                        $this->permissionRoles[$_role][$permission->code] = 1;
                    }
                }
                else {
                    $this->permissionRoles['*'][$permission->code] = 1;
                }
            }
        }

        $result = $this->permissionRoles[$role] ?? [];

        if ($includeOrphans) {
            $result += $this->permissionRoles['*'] ?? [];
        }

        return $result;
    }

    /**
     * hasPermissionsForRole checks if the user has the permissions for a role.
     */
    public function hasPermissionsForRole($role): bool
    {
        return !!$this->listPermissionsForRole($role, false);
    }
}
