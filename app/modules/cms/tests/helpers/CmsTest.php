<?php

use Cms\Helpers\Cms as CmsHelper;

class CmsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Config::set('cms.url_exceptions', [
            '/api/*' => 'maintenance',
            '/sitemap.xml' => 'site|maintenance',
            '/landing-page' => 'site',
        ]);
    }

    public function testMatchUrlException()
    {
        $helper = new CmsHelper;
        $this->assertFalse($helper->urlHasException('/api/ping', 'site'));
        $this->assertTrue($helper->urlHasException('/api/ping', 'maintenance'));
        $this->assertFalse($helper->urlHasException('api/users', 'site'));
        $this->assertTrue($helper->urlHasException('api/users', 'maintenance'));
        $this->assertTrue($helper->urlHasException('api/users/', 'maintenance'));
        $this->assertFalse($helper->urlHasException('/api', 'maintenance'));
        $this->assertFalse($helper->urlHasException('api', 'maintenance'));
        $this->assertFalse($helper->urlHasException('/landing-page', 'maintenance'));
        $this->assertTrue($helper->urlHasException('/landing-page', 'site'));
        $this->assertFalse($helper->urlHasException('/landing-page/foobar', 'site'));
        $this->assertTrue($helper->urlHasException('/sitemap.xml', 'site'));
        $this->assertTrue($helper->urlHasException('/sitemap.xml', 'maintenance'));
        $this->assertFalse($helper->urlHasException('/sitemap.xml', 'foobar'));
    }
}
