<?php namespace Lovata\MightySeo\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Lovata\Toolbox\Classes\Helper\PageHelper;

/**
 * Class SeoPageParams
 * @package Lovata\MightySeo\Controllers
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class SeoPageParams extends Controller
{
    public $implement = [
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.FormController',
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    /**
     * SeoPageParams constructor.
     */
    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Lovata.MightySeo', 'menu-mighty-seo-main', 'menu-mighty-seo-page');
    }

    /**
     * @param \Lovata\MightySeo\Models\SeoParam $obQuery
     */
    public function listExtendQuery($obQuery)
    {
        $obQuery->whereNull('external_id');
    }

    /**
     * @param $obFilter
     */
    public function listFilterExtendScopes($obFilter)
    {
        $obFilter->addScopes([
            'page_list' => [
                'label'      => 'lovata.mightyseo::lang.field.page_id',
                'type'       => 'group',
                'conditions' => 'page_id in (:filtered)',
                'options'    => PageHelper::instance()->getPageNameList(),
            ]
        ]);
    }
}
