<?php namespace Lovata\Shopaholic\Updates;

use Seeder;
use System\Models\MailTemplate;

/**
 * Class SeederUpdateEmailTemplate
 * @package Lovata\Shopaholic\Updates
 */
class SeederUpdateEmailTemplate extends Seeder
{
    /**
     * Run seeder
     */
    public function run()
    {
        $obMailTemplate = MailTemplate::where('code', 'lovata.formit.contact_form')->first();

        if (empty($obMailTemplate)) {
            return;
        }

        try {
            $obMailTemplate->subject = 'Incoming Request: Contact Form {{ data.name }} (lovata.com)';
            $obMailTemplate->content_html = 'Contact form data:<br>
Name: {{ data.name }}<br>
Phone: {{ data.phone }}<br>
Email: <a href="mailto:{{ data.email }}">{{ data.email }}</a><br>
Message: {{ data.comment }}';
            $obMailTemplate->save();
        } catch (\Exception $obException) {}
    }
}
