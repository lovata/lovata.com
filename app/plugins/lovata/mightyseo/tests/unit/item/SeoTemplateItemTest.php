<?php namespace Lovata\MightySeo\Tests\Unit\Item;

use Lovata\Toolbox\Tests\CommonTest;

use Lovata\MightySeo\Models\SeoTemplate;
use Lovata\MightySeo\Classes\Item\SeoTemplateItem;

/**
 * Class SeoTemplateItemTest
 * @package Lovata\MightySeo\Tests\Unit\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class SeoTemplateItemTest extends CommonTest
{
    /** @var  SeoTemplate */
    protected $obElement;

    protected $arCreateData = [
        'key'   => 'key',
        'value' => 'value',
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

        $sErrorMessage = 'SeoTemplate item data is not correct';

        $arCreatedData = $this->arCreateData;
        $arCreatedData['id'] = $this->obElement->id;

        //Check item fields
        $obItem = SeoTemplateItem::make($this->obElement->key);
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

        $sErrorMessage = 'SeoTemplate item data is not correct, after model update';

        $obItem = SeoTemplateItem::make($this->obElement->key);
        self::assertEquals('key', $obItem->key, $sErrorMessage);

        //Check cache update
        $this->obElement->key = 'test';
        $this->obElement->save();

        $obItem = SeoTemplateItem::make($this->obElement->key);
        self::assertEquals('test', $obItem->key, $sErrorMessage);
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

        $sErrorMessage = 'SeoTemplate item data is not correct, after model remove';

        $obItem = SeoTemplateItem::make($this->obElement->key);
        self::assertEquals(false, $obItem->isEmpty(), $sErrorMessage);

        //Remove element
        $this->obElement->delete();

        $obItem = SeoTemplateItem::make($this->obElement->key);
        self::assertEquals(true, $obItem->isEmpty(), $sErrorMessage);
    }

    /**
     * Create brand object for test
     */
    protected function createTestData()
    {
        //Create new element data
        $arCreateData = $this->arCreateData;
        $this->obElement = SeoTemplate::create($arCreateData);
    }
}