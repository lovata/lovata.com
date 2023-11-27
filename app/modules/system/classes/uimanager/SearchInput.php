<?php namespace System\Classes\UiManager;

use Html;
use System\Classes\UiElement;

/**
 * SearchInput
 *
 * @method SearchInput ajaxHandler(string $ajaxHandler) ajaxHandler submits searches using an AJAX handler.
 * @method SearchInput searchOnEnter(string $searchOnEnter) searchOnEnter searches on enter key instead of every key stroke.
 * @method SearchInput placeholder(string $placeholder) placeholder for the input
 * @method SearchInput name(string $name) name for data value
 * @method SearchInput value(string $value) value presets the data value
 * @method SearchInput isModal() isModal removes borders for use in a modal
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class SearchInput extends UiElement
{
    /**
     * __construct
     */
    public function __construct($placeholder = 'Search...', $name = 'term', $config = [])
    {
        $this->placeholder($placeholder);
        $this->name($name);

        parent::__construct($config);
    }

    /**
     * render the element
     */
    public function render(): callable
    {
        return function() { ?>
            <div data-control="searchinput" class="<?= $this->isModal ? 'is-modal-search' : '' ?>">
                <div class="search-input-container storm-icon-pseudo">
                    <input
                        <?= Html::attributes($this->buildAttributes()) ?>
                        class="form-control"
                        autocomplete="off"
                        data-search-input />
                    <button
                        class="clear-input-text"
                        type="button"
                        value=""
                        style="display: none"
                        data-search-clear
                    >
                        <i class="storm-icon"></i>
                    </button>
                </div>
            </div>
        <?php };
    }


    /**
     * buildAttributes
     */
    protected function buildAttributes(array $attr = []): array
    {
        $attr['name'] = $this->name;
        $attr['placeholder'] = $this->placeholder;
        $attr['type'] = $this->type ?: 'text';
        $attr['value'] = $this->value ?: '';

        if ($this->placeholder) {
            $attr['placeholder'] = $this->placeholder;
        }

        if ($this->ajaxHandler) {
            $attr['data-request'] = $this->ajaxHandler;
            $attr['data-load-indicator'] = true;
            $attr['data-load-indicator-opaque'] = true;

            if (!$this->searchOnEnter) {
                $attr['data-track-input'] = true;
            }

        }

        return $attr;
    }
}
