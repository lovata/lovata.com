<?php namespace Lovata\FeedbackForCrm\Classes\Console;

use Illuminate\Console\Command;
use Lovata\FeedbackForCrm\Classes\Helper\AmoCrm\ApiAmoCRM;

/**
 * Class RefreshTokenRefreshToken
 * @package Lovata\FeedbackForCrm\Classes\Console
 * @author  Sergey Zakharevich, s.zakharevich@lovata.com, LOVATA Group
 */
class RefreshTokenRefreshToken extends Command
{
    /** @var string */
    protected $name = 'feedback_for_crm:amo_crm_refresh_token';
    /** @var string */
    protected $description = 'Refresh token for AmoCRM';

    /**
     * Execute the console command.
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        $obApiAmoCrm = new ApiAmoCRM();
        $obApiAmoCrm->refreshToken();
    }
}
