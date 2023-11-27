<?php namespace System\Classes\UiManager;

/**
 * PopupButton
 *
 * @method PopupButton ajaxHandler(string $ajaxHandler) ajaxHandler
 * @method PopupButton ajaxData(array $ajaxData) ajaxData
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class PopupButton extends Button
{
    /**
     * __construct
     */
    public function __construct($label = 'Button', $ajaxHandler = 'onAjax', $config = [])
    {
        $this->ajaxHandler($ajaxHandler);

        parent::__construct($label, $config);
    }

    /**
     * buildAttributes
     */
    protected function buildAttributes(array $attr = []): array
    {
        $attr = parent::buildAttributes($attr);

        $attr['data-control'] = 'popup';

        $attr['data-handler'] = $this->ajaxHandler;

        if ($this->ajaxData !== null) {
            $attr['data-request-data'] = $this->ajaxData;
        }

        return $attr;
    }
}
