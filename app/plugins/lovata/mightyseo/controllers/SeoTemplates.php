<?php namespace Lovata\MightySeo\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Class SeoTemplates
 * @package Lovata\MightySeo\Controllers
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class SeoTemplates extends Controller
{
    public $implement = [
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.FormController',
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    /**
     * SeoTemplates constructor.
     */
    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Lovata.MightySeo', 'menu-mighty-seo-main', 'menu-mighty-seo-template');
    }

    /**
     * @param \Lovata\MightySeo\Models\SeoParam $obQuery
     */
    public function listExtendQuery($obQuery)
    {
        $obQuery->whereNull('external_id');
    }
}