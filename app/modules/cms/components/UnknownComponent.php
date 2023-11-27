<?php namespace Cms\Components;

use Cms\Classes\ComponentModuleBase;

class UnknownComponent extends ComponentModuleBase
{
    /**
     * @var string errorMessage that is shown with this error component.
     */
    protected $errorMessage;

    /**
     * __construct
     */
    public function __construct($cmsObject, $properties, $errorMessage)
    {
        $this->errorMessage = $errorMessage;
        $this->componentCssClass = 'error-component';
        $this->inspectorEnabled = false;

        parent::__construct($cmsObject, $properties);
    }

    /**
     * componentDetails
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name' => 'Unknown component',
            'description' => $this->errorMessage
        ];
    }
}
