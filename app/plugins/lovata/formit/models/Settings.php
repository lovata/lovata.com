<?php namespace Lovata\FormIt\Models;

use October\Rain\Database\Model;

/**
 * Class Settings
 * @package Lovata\FormIt\Models
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];
    public $settingsCode = 'lovata_form_it_settings';
    public $settingsFields = 'fields.yaml';
}