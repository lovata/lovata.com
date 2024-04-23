<?php namespace Backend\Classes;

use Html;
use October\Rain\Element\Navigation\ItemDefinition;

/**
 * SideMenuItem
 *
 * @method SideMenuItem owner(string $owner) owner
 * @method SideMenuItem iconSvg(null|string $iconSvg) iconSvg
 * @method SideMenuItem counter(null|int|callable $counter) counter
 * @method SideMenuItem counterLabel(null|string $counterLabel) counterLabel
 * @method SideMenuItem attributes(array $attributes) attributes
 * @method SideMenuItem permissions(array $permissions) permissions
 * @method SideMenuItem itemType(string $itemType) itemType
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class SideMenuItem extends ItemDefinition
{
    /**
     * initDefaultValues for this scope
     */
    protected function initDefaultValues()
    {
        parent::initDefaultValues();

        $this
            ->attributes([])
            ->permissions([])
        ;
    }

    /**
     * addAttribute
     * @param null|string|int $attribute
     * @param null|string|array $value
     */
    public function addAttribute($attribute, $value)
    {
        $this->config['attributes'][$attribute] = $value;
    }

    /**
     * removeAttribute
     */
    public function removeAttribute($attribute)
    {
        unset($this->config['attributes'][$attribute]);
    }

    /**
     * addPermission
     * @deprecated recommend not using this method until v4 when signature is fixed
     * should be a non-associative array
     */
    public function addPermission(string $permission, array $definition)
    {
        $this->config['permissions'][$permission] = $definition;
    }

    /**
     * removePermission
     * @deprecated recommend not using this method until v4 when signature is fixed
     * should spin over every value and remove via located key
     * @param string $permission
     * @return void
     */
    public function removePermission(string $permission)
    {
        unset($this->config['permissions'][$permission]);
    }

    /**
     * itemAttributes returns HTML attributes for the list item
     */
    public function itemAttributes(): string
    {
        if ($this->attributes === null) {
            return '';
        }

        return Html::attributes(array_except($this->attributes, ['target']));
    }

    /**
     * linkAttributes returns HTML for the anchor link
     */
    public function linkAttributes(): string
    {
        if (!isset($this->attributes['target'])) {
            return '';
        }

        return Html::attributes(array_only($this->attributes, ['target']));
    }
}
