<?php namespace Tailor\Classes;

use Backend;
use BackendMenu;
use October\Rain\Element\Navigation\ItemDefinition;
use System\Classes\SettingsManager;

/**
 * NavigationItem
 *
 * @method NavigationItem mode(string $mode) mode can be: content, settings, primary, secondary
 * @method NavigationItem category(string $category) category is used by settings modes
 * @method NavigationItem description(string $description) description is used by settings modes
 * @method NavigationItem uuid(string $uuid) uuid is uuid sourced from the blueprint
 * @method NavigationItem handle(string $handle) handle sourced from the blueprint
 * @method NavigationItem parent(string $parent) parent is a parent uuid and used by secondary modes
 * @method NavigationItem parentCode(string $parentCode) parentCode is a parent code and used by secondary modes
 * @method NavigationItem permissionCode(string $permissionCode) permissionCode for a secondary item
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class NavigationItem extends ItemDefinition
{
    const MODE_CONTENT = 'content';
    const MODE_SETTINGS = 'settings';
    const MODE_PRIMARY = 'primary';
    const MODE_SECONDARY = 'secondary';

    /**
     * initDefaultValues for this scope
     */
    protected function initDefaultValues()
    {
        parent::initDefaultValues();

        $this
            ->icon('icon-copy')
        ;
    }

    /**
     * evalConfig from an array and apply them to the object
     */
    public function evalConfig(array $config)
    {
        $this->mode = $this->processItemMode($this->config);
    }

    /**
     * processItemMode
     */
    protected function processItemMode($config)
    {
        $parent = $config['parent'] ?? null;
        if ($parent === static::MODE_SETTINGS || $parent === static::MODE_CONTENT) {
            return $parent;
        }

        if ($parent) {
            return static::MODE_SECONDARY;
        }

        if ($config['hasPrimary'] ?? false) {
            return static::MODE_PRIMARY;
        }

        return $config['mode'] ?? 'content';
    }

    /**
     * toBackendMenuArray
     */
    public function toBackendMenuArray(): array
    {
        $result = [
            'label' => $this->label,
            'icon' => $this->icon,
            'iconSvg' => $this->iconSvg,
            'url' => Backend::url($this->url),
            'order' => $this->order,
        ];

        if ($this->permissionCode) {
            $result['permissions'] = (array) $this->permissionCode;
        }

        return $result;
    }

    /**
     * toBackendSettingsArray
     */
    public function toBackendSettingsArray(): array
    {
        $result = [
            'label' => $this->label,
            'description' => $this->description,
            'category' => $this->category,
            'icon' => $this->icon,
            'url' => Backend::url($this->url),
            'order' => $this->order,
        ];

        if ($this->permissionCode) {
            $result['permissions'] = (array) $this->permissionCode;
        }

        return $result;
    }

    /**
     * setBackendControllerContext
     */
    public function setBackendControllerContext()
    {
        if ($this->mode === static::MODE_SETTINGS) {
            BackendMenu::setContext('October.System', 'system', 'settings');
            SettingsManager::setContext('October.Tailor', $this->code);
        }
        elseif ($this->mode === static::MODE_CONTENT) {
            BackendMenu::setContext('October.Tailor', 'tailor', $this->code);
        }
        elseif ($this->mode === static::MODE_PRIMARY) {
            BackendMenu::setContext('October.Tailor', $this->code, $this->code);
        }
        elseif ($this->mode === static::MODE_SECONDARY) {
            BackendMenu::setContext('October.Tailor', $this->parentCode, $this->code);
        }
    }
}
