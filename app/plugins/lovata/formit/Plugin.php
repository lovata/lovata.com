<?php namespace Lovata\FormIt;

use System\Classes\PluginBase;

/**
 * Class Plugin
 * @package Lovata\FormIt
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Plugin extends PluginBase
{
    /**
     * @return array
     */
    public function registerComponents()
    {
        return [
            '\Lovata\FormIt\Components\FormIt' => 'FormIt',
        ];
    }

    /**
     * @return array
     */
    public function registerSettings()
    {
        return [
            'config' => [
                'label'       => 'lovata.formit::lang.plugin.label',
                'icon'        => 'icon-envelope-o',
                'description' => 'lovata.formit::lang.plugin.description',
                'class'       => 'Lovata\FormIt\Models\Settings',
                'order'       => 100
            ]
        ];
    }
}