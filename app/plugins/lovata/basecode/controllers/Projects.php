<?php namespace Lovata\BaseCode\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Class Projects
 * @package Lovata\BaseCode\Controllers
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Projects extends Controller
{
    /** @var array */
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
    ];
    /** @var string */
    public $listConfig = 'config_list.yaml';
    /** @var string */
    public $formConfig = 'config_form.yaml';

    /**
     * Articles constructor.
     */
    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext(
            'Lovata.BaseCode',
            'main-basecode',
            'side-basecode-project'
        );
    }
}
