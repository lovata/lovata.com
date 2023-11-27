<?php namespace Lovata\FormIt\Components;

use Log;
use Mail;
use Input;
use Cms\Classes\ComponentBase;
use Lovata\FormIt\Models\Settings;
use October\Rain\Database\Collection;
use System\Models\MailTemplate;

/**
 * Class FormIt
 * @package Lovata\FormIt\Components
 */
class FormIt extends ComponentBase
{
    /** @var string */
    protected $sGroupCode;

    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'lovata.formit::lang.component.form_it',
            'description' => 'lovata.formit::lang.component.form_it_desc'
        ];
    }

    /**
     * @return array
     */
    public function defineProperties()
    {
        return [
            'template' => [
                'title' => 'lovata.formit::lang.component.property_template_id',
                'type'  => 'dropdown',
            ]
        ];
    }

    /**
     * Get mail template list
     * @return array
     */
    public function getTemplateOptions()
    {
        /** @var Collection $arMailTemplates */
        $arMailTemplates = MailTemplate::where('code', 'LIKE', 'lovata.formit%')->get();
        if($arMailTemplates->isEmpty()) {
            return [];
        }

        $arResult = [];

        /** @var MailTemplate $obMailTemplate */
        foreach($arMailTemplates as $obMailTemplate) {
            $arResult[$obMailTemplate->code] = $obMailTemplate->subject;
        }

        return $arResult;
    }

    /**
     * Send mail
     * @return bool
     */
    public function onSend()
    {
        $sTemplate = $this->property('template');
        if(empty($sTemplate)) {
            return false;
        }

        //Get form data
        $arData = Input::get('form');
        if(empty($arData)) {
            return false;
        }
        
        //Get "send to" email 
        if(isset($arData['send_to']) && !empty($arData['send_to'])) {
            $sSendTo = $arData['send_to'];
        } else {
            $sSendTo = Settings::get('send_to');
        }
        
        Log::info('contact_form: '.json_encode($arData));
        
        if(empty($sSendTo)) {
            return false;
        }
        
        $obFile = null;
        if(Input::hasFile('form_file')) {
            $obFile = Input::file('form_file');
        }
        
        $arSendToList = explode(',', $sSendTo);
        foreach($arSendToList as $sEmail) {
            $sEmail = trim($sEmail);
            if(empty($sEmail)) {
                continue;
            }

            Mail::send($sTemplate, $arData, function ($obMessage) use ($sEmail, $obFile) {
                $obMessage->to($sEmail);

                if(!empty($obFile)) {
                    $obMessage->attach(
                        $obFile->getRealPath(),
                        [
                            'as' => $obFile->getClientOriginalName(),
                            'mime' => $obFile->getClientMimeType(),
                        ]
                    );
                }
            });
        }

        return true;
    }
}
