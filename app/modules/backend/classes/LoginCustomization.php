<?php namespace Backend\Classes;

use Url;
use Backend\Models\BrandSetting;
use Exception;
use File;

/**
 * LoginCustomization
 */
class LoginCustomization
{
    /**
     * getCustomizationVariables
     */
    public static function getCustomizationVariables($controller)
    {
        $result = [];

        try {
            $result['logo'] = BrandSetting::getLogo();
        }
        catch (Exception $ex) {
            $result['logo'] = BrandSetting::getDefaultLogo();
        }

        if (!$result['logo']) {
            $result['logo'] = Url::asset('/modules/backend/assets/images/october-logo.svg');
        }

        $result['loginCustomization'] = BrandSetting::getLoginPageCustomization();

        $defaultImageNum = rand(1, 5);
        $result['defaultImage1x'] = $defaultImageNum.'.png';
        $result['defaultImage2x'] = $defaultImageNum.'@2x.png';

        return (object)$result;
    }

    public static function getGeneratedImageData()
    {
        $index = rand(1, 7);

        $basePath = base_path() . '/modules/backend/assets/images/october-login-ai-generated/';
        $backgroundPath = $basePath . $index . '/background.css';

        return (object)[
            'img' => $index.'/image.png',
            'background' => File::get($backgroundPath)
        ];
    }
}
