<?php namespace Lovata\MightySeo\Tests\Unit\Models;

include_once __DIR__.'/../../../../../../tests/PluginTestCase.php';

use PluginTestCase;
use Lovata\MightySeo\Models\SeoParam;

/**
 * Class SeoParamTest
 * @package Lovata\MightySeo\Tests\Unit\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class SeoParamTest extends PluginTestCase
{
    protected $sModelClass;

    /**
     * SeoParamTest constructor.
     */
    public function __construct()
    {
        $this->sModelClass = SeoParam::class;
        parent::__construct();
    }

    /**
     * Check model "external" relation config
     */
    public function testHasProductRelation()
    {
        $sErrorMessage = $this->sModelClass.' model has not correct "external" relation config';

        /** @var SeoParam $obModel */
        $obModel = new SeoParam();
        self::assertNotEmpty($obModel->morphTo, $sErrorMessage);
        self::assertArrayHasKey('external', $obModel->morphTo, $sErrorMessage);
    }
}
