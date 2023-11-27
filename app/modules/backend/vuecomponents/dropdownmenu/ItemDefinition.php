<?php namespace Backend\VueComponents\DropdownMenu;

use SystemException;

/**
 * ItemDefinition encapsulates Dropdown menu item information.
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class ItemDefinition
{
    const TYPE_TEXT = 'text';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_RADIOBUTTON = 'radiobutton';
    const TYPE_SEPARATOR = 'separator';

    /**
     * @var bool disabled
     */
    protected $disabled = false;

    /**
     * @var string type
     */
    protected $type = ItemDefinition::TYPE_TEXT;

    /**
     * @var string label
     */
    protected $label;

    /**
     * @var string linkHref
     */
    protected $linkHref;

    /**
     * @var string linkTarget
     */
    protected $linkTarget;

    /**
     * @var string icon
     */
    protected $icon;

    /**
     * @var string command
     */
    protected $command;

    /**
     * @var array items
     */
    protected $items = [];

    /**
     * @var bool checked
     */
    protected $checked = false;

    /**
     * @var array|null userData
     */
    protected $userData = null;

    /**
     * @var string|null key
     */
    protected $key = null;

    /**
     * @var string|null group
     */
    protected $group;

    /**
     * __construct
     */
    public function __construct($type, string $label = null, string $command = null)
    {
        $this->type = $type;

        if ($type != ItemDefinition::TYPE_SEPARATOR && !strlen($label)) {
            throw new SystemException('Dropdown menu item label is not provided for item type '.$type);
        }

        if ($type != ItemDefinition::TYPE_SEPARATOR && !strlen($command)) {
            throw new SystemException('Dropdown menu item command is not provided for item type '.$type);
        }

        $this->label = $label;
        $this->command = $command;
    }

    /**
     * Sets optional item key attribute
     */
    public function setKey(string $value)
    {
        $this->key = $value;
    }

    /**
     * setLinkHref
     */
    public function setLinkHref(string $value)
    {
        $this->linkHref = $value;

        return $this;
    }

    /**
     * setLinkTarget
     */
    public function setLinkTarget(string $value)
    {
        $this->linkTarget = $value;

        return $this;
    }

    /**
     * setDisabled
     */
    public function setDisabled(bool $value)
    {
        $this->disabled = $value;

        return $this;
    }

    /**
     * setIcon
     */
    public function setIcon(string $value)
    {
        if (in_array($this->type, [self::TYPE_CHECKBOX, self::TYPE_RADIOBUTTON])) {
            throw new SystemException('Checkbox and radiobutton dropdown menu items cannot have icons');
        }

        $this->icon = $value;

        return $this;
    }

    /**
     * setChecked
     */
    public function setChecked(bool $value)
    {
        $this->checked = $value;
    }

    /**
     * addItem
     */
    public function addItem($type, string $label = null, string $command = null)
    {
        return $this->items[] = new ItemDefinition($type, $label, $command);
    }

    /**
     * addItemObject
     */
    public function addItemObject(ItemDefinition $item)
    {
        return $this->items[] = $item;
    }

    /**
     * hasItems
     */
    public function hasItems()
    {
        return count($this->items) > 0;
    }

    /**
     * Sets optional user data object.
     */
    public function setUserData(array $userData)
    {
        $this->userData = $userData;

        return $this;
    }

    /**
     * setGroup
     */
    public function setGroup(string $group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * toArray
     */
    public function toArray()
    {
        $result = [
            'type' => $this->type,
            'icon' => $this->icon,
            'command' => $this->command,
            'label' => $this->label,
            'href' => $this->linkHref,
            'target' => $this->linkTarget,
            'disabled' => $this->disabled,
            'checked' => $this->checked
        ];

        if ($this->group) {
            $result['group'] = $this->group;
        }

        if ($this->userData) {
            $result['userData'] = $this->userData;
        }

        if ($this->key) {
            $result['key'] = $this->key;
        }

        $result['items'] = [];

        if ($this->items) {
            $subItems = [];

            foreach ($this->items as $item) {
                $subItems[] = $item->toArray();
            }

            $result['items'] = $subItems;
        }

        return $result;
    }
}
