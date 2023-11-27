<?php namespace Backend\Traits;

use Input;

/**
 * SelectableWidget trait adds item selection features to back-end widgets
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
trait SelectableWidget
{
    /**
     * @var bool selectedItemsCache
     */
    protected $selectedItemsCache = false;

    /**
     * @var string selectionInputName
     */
    protected $selectionInputName = 'object';

    /**
     * onSelect
     */
    public function onSelect()
    {
        $this->extendSelection();
    }

    /**
     * getSelectedItems
     */
    protected function getSelectedItems()
    {
        if ($this->selectedItemsCache !== false) {
            return $this->selectedItemsCache;
        }

        $items = $this->getSession('selected', []);
        if (!is_array($items)) {
            return $this->selectedItemsCache = [];
        }

        return $this->selectedItemsCache = $items;
    }

    /**
     * extendSelection
     */
    protected function extendSelection()
    {
        $items = (array) Input::get($this->selectionInputName, []);
        $currentSelection = $this->getSelectedItems();

        $this->putSession('selected', $currentSelection + $items);
    }

    /**
     * resetSelection
     */
    protected function resetSelection()
    {
        $this->putSession('selected', []);
    }

    /**
     * removeSelection
     */
    protected function removeSelection($itemId)
    {
        $currentSelection = $this->getSelectedItems();

        unset($currentSelection[$itemId]);
        $this->putSession('selected', $currentSelection);
        $this->selectedItemsCache = $currentSelection;
    }

    /**
     * isItemSelected
     */
    protected function isItemSelected($itemId)
    {
        $selectedItems = $this->getSelectedItems();
        if (!is_array($selectedItems) || !isset($selectedItems[$itemId])) {
            return false;
        }

        return $selectedItems[$itemId];
    }
}
