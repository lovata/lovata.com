<?php namespace Backend\VueComponents\TreeView;

use Backend\VueComponents\DropdownMenu\ItemDefinition;

/**
 * SectionDefinition encapsulates Treeview section information.
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class SectionDefinition
{
    /**
     * @var string key
     */
    protected $key;

    /**
     * @var mixed menuItems
     */
    protected $menuItems = null;

    /**
     * @var mixed createMenuItems
     */
    protected $createMenuItems = null;

    /**
     * @var string label
     */
    protected $label;

    /**
     * @var array nodes
     */
    protected $nodes = [];

    /**
     * @var mixed childKeyPrefix
     */
    protected $childKeyPrefix;

    /**
     * @var bool hasApiMenuItems
     */
    protected $hasApiMenuItems = false;

    /**
     * @var mixed userData
     */
    protected $userData = null;

    /**
     * __construct
     */
    public function __construct(string $label, string $key)
    {
        $this->label = $label;
        $this->key = $key;
    }

    /**
     * addNode
     */
    public function addNode(string $label, string $key)
    {
        if (strlen($this->childKeyPrefix)) {
            $key = $this->childKeyPrefix.$key;
        }

        $node = new NodeDefinition($label, $key);

        $node->setChildKeyPrefix($this->childKeyPrefix);

        return $this->nodes[] = $node;
    }

    /**
     * setChildKeyPrefix
     */
    public function setChildKeyPrefix($prefix)
    {
        $this->childKeyPrefix = $prefix;

        return $this;
    }

    /**
     * addMenuItem
     */
    public function addMenuItem($type, string $label = null, string $command = null)
    {
        if (!$this->menuItems) {
            $this->menuItems = new ItemDefinition(ItemDefinition::TYPE_TEXT, 'root', 'none');
        }

        return $this->menuItems->addItem($type, $label, $command);
    }

    /**
     * addMenuItemObject
     */
    public function addMenuItemObject(ItemDefinition $item)
    {
        if (!$this->menuItems) {
            $this->menuItems = new ItemDefinition(ItemDefinition::TYPE_TEXT, 'root', 'none');
        }

        return $this->menuItems->addItemObject($item);
    }

    /**
     * addCreateMenuItem
     */
    public function addCreateMenuItem($type, string $label = null, string $command = null)
    {
        if (!$this->createMenuItems) {
            $this->createMenuItems = new ItemDefinition(ItemDefinition::TYPE_TEXT, 'root', 'none');
        }

        return $this->createMenuItems->addItem($type, $label, $command);
    }

    /**
     * Indicates that the section supports API-generated menu items.
     */
    public function setHasApiMenuItems(bool $hasApiMenuItems)
    {
        $this->hasApiMenuItems = $hasApiMenuItems;

        return $this;
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
     * setUserDataElement
     */
    public function setUserDataElement(string $key, $value)
    {
        if (!is_array($this->userData)) {
            $this->userData = [];
        }

        $this->userData[$key] = $value;
        return $this;
    }

    /**
     * getNodes
     */
    public function getNodes()
    {
        return $this->nodes;
    }

    /**
     * toArray
     */
    public function toArray()
    {
        $result = [
            'label' => $this->label,
            'uniqueKey' => $this->key
        ];

        if ($this->hasApiMenuItems) {
            $result['hasApiMenuItems'] = $this->hasApiMenuItems;
        }
        else {
            $result['hasApiMenuItems'] = false;
        }

        if ($this->userData) {
            $result['userData'] = $this->userData;
        }

        $result['nodes'] = [];

        foreach ($this->nodes as $node) {
            $result['nodes'][] = $node->toArray();
        }

        if ($this->menuItems) {
            $menuItems = $this->menuItems->toArray();
            $result['menuItems'] = $menuItems['items'];
        }

        if ($this->createMenuItems) {
            $createMenuItems = $this->createMenuItems->toArray();
            $result['createMenuItems'] = $createMenuItems['items'];
        }

        return $result;
    }
}
