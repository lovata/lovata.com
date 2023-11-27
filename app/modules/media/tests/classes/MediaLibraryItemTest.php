<?php

use Media\Classes\MediaLibraryItem;

/**
 * MediaLibraryItemTest
 */
class MediaLibraryItemTest extends TestCase
{
    /**
     * setUp test
     */
    public function setUp(): void
    {
        MediaLibraryItem::forgetExtensions();
        parent::setUp();
    }

    /**
     * testFileTypeImage checks standard file type
     */
    public function testFileTypeImage()
    {
        $item = new MediaLibraryItem('/demo/pictures/image19.jpg', 1000, 1654168594, MediaLibraryItem::TYPE_FILE, 'https://localhost/demo/pictures/image19.jpg');

        $this->assertTrue($item->isFile());
        $this->assertEquals(MediaLibraryItem::FILE_TYPE_IMAGE, $item->getFileType());
        $this->assertNotEquals(MediaLibraryItem::FILE_TYPE_DOCUMENT, $item->getFileType());
    }

    /**
     * testFileTypeCustom checks custom type registration
     */
    public function testFileTypeCustom()
    {
        $this->app['config']->set('media.video_extensions', ['xxx', 'mp4', 'avi', 'mov', 'mpg', 'mpeg', 'mkv', 'webm']);

        $item = new MediaLibraryItem('/demo/pictures/video19.xxx', 1000, 1654168594, MediaLibraryItem::TYPE_FILE, 'https://localhost/demo/pictures/video19.xxx');

        $this->assertTrue($item->isFile());
        $this->assertEquals(MediaLibraryItem::FILE_TYPE_VIDEO, $item->getFileType());
        $this->assertNotEquals(MediaLibraryItem::FILE_TYPE_DOCUMENT, $item->getFileType());
    }

    /**
     * testItemTypeFolder
     */
    public function testItemTypeFolder()
    {
        $item = new MediaLibraryItem('/demo/pictures', 26, 1654168594, MediaLibraryItem::TYPE_FOLDER, 'https://localhost/demo/pictures');

        $this->assertFalse($item->isFile());
        $this->assertNull($item->getFileType());
        $this->assertEquals('26 item(s)', $item->sizeToString());
    }
}
