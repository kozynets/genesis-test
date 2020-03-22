<?php declare(strict_types=1);

use genesis\cache\variants\FileVariantCache;
use PHPUnit\Framework\TestCase;

class FileCacheTest extends TestCase
{
    private $fileCache;

    protected function setUp(): void
    {
        $this->fileCache = $this->getMockBuilder(FileVariantCache::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'set', 'exists', '__destruct', 'saveFileData', 'getFileData'])
            ->getMock();
    }

    public function testGet()
    {

    }
}