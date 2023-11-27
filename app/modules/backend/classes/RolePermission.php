<?php namespace Backend\Classes;

use October\Rain\Element\ElementBase;

/**
 * RolePermission
 *
 * @method RolePermission code(string $code) code
 * @method RolePermission label(string $label) label
 * @method RolePermission comment(string $comment) comment
 * @method RolePermission children(array $children) children
 * @method RolePermission roles(array $roles) roles
 * @method RolePermission order(int $order) order
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class RolePermission extends ElementBase
{
    /**
     * initDefaultValues override method
     */
    protected function initDefaultValues()
    {
        $this->order(500);
    }

    /**
     * addChild
     */
    public function addChild($permission): static
    {
        $children = $this->children;

        $children[$permission->code] = $permission;

        $this->children($children);

        return $this;
    }
}
