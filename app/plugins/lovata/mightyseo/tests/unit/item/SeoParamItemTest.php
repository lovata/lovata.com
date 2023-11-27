<?php namespace Lovata\MightySeo\Tests\Unit\Item;

use Lovata\Toolbox\Tests\CommonTest;

use Lovata\MightySeo\Models\SeoParam;
use Lovata\MightySeo\Classes\Item\SeoParamItem;

/**
 * Class SeoParamItemTest
 * @package Lovata\MightySeo\Tests\Unit\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class SeoParamItemTest extends CommonTest
{
    /** @var  SeoParam */
    protected $obElement;

    protected $arCreateData = [
        'page_id'               => 'page_id',
        'seo_title'             => 'seo_title',
        'seo_description'       => 'seo_description',
        'seo_keywords'          => 'seo_keywords',
        'page_h1'               => 'page_h1',
        'page_description'      => 'page_description',
        'robots_follow'         => 'robots_follow',
        'robots_index'          => 'robots_index',
        'robots_index_addition' => 'robots_index_addition',
        'canonical_url'         => 'canonical_url',
    ];

    /**
     * Check item fields
     */
    public function testItemFields()
    {
        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'SeoParam item data is not correct';

        $arCreatedData = $this->arCreateData;
        $arCreatedData['id'] = $this->obElement->id;

        //Check item fields
        $obItem = SeoParamItem::make($this->obElement->id);
        foreach ($arCreatedData as $sField => $sValue) {
            self::assertEquals($sValue, $obItem->$sField, $sErrorMessage);
        }
    }

    /**
     * Check update cache item data, after update model data
     */
    public function testItemClearCache()
    {
        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'SeoParam item data is not correct, after model update';

        $obItem = SeoParamItem::make($this->obElement->id);
        self::assertEquals('seo_title', $obItem->seo_title, $sErrorMessage);

        //Check cache update
        $this->obElement->seo_title = 'test';
        $this->obElement->save();

        $obItem = SeoParamItem::make($this->obElement->id);
        self::assertEquals('test', $obItem->seo_title, $sErrorMessage);
    }

    /**
     * Check update cache item data, after remove element
     */
    public function testRemoveElement()
    {
        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'SeoParam item data is not correct, after model remove';

        $obItem = SeoParamItem::make($this->obElement->id);
        self::assertEquals(false, $obItem->isEmpty(), $sErrorMessage);

        //Remove element
        $this->obElement->delete();

        $obItem = SeoParamItem::make($this->obElement->id);
        self::assertEquals(true, $obItem->isEmpty(), $sErrorMessage);
    }

    /**
     * Create brand object for test
     */
    protected function createTestData()
    {
        //Create new element data
        $arCreateData = $this->arCreateData;
        $this->obElement = SeoParam::create($arCreateData);
    }
}