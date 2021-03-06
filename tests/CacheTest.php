<?php declare(strict_types=1);

use genesis\cache\Cache;
use genesis\cache\variants\FileVariantCache;
use genesis\cache\variants\StaticVariantCache;
use genesis\exceptions\CacheNotFoundException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class CacheTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $staticCache;

    /**
     * @var MockObject
     */
    private $fileCache;

    /**
     * @var MockObject
     */
    private $cache;

    protected function setUp(): void
    {
        $this->staticCache = $this->getMockBuilder(StaticVariantCache::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'set', 'exists'])
            ->getMock();

        $this->fileCache = $this->getMockBuilder(FileVariantCache::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'set', 'exists', '__destruct'])
            ->getMock();

        $this->cache = new Cache($this->staticCache, $this->fileCache);
    }

    public function testGetCacheWithStatic()
    {
        $this->staticCache->expects($this->once())->method('exists')
            ->with($this->equalTo('var1'))->will($this->returnValue(true));
        $this->staticCache->expects($this->once())->method('get')
            ->with($this->equalTo('var1'))->will($this->returnValue('test'));
        $result = $this->cache->get('var1');
        $this->assertEquals('test', $result);
    }

    public function testGetCacheWithoutStatic()
    {
        $this->staticCache->expects($this->once())->method('exists')
            ->with($this->equalTo('var1'))->will($this->returnValue(false));
        $this->fileCache->expects($this->once())->method('exists')
            ->with($this->equalTo('var1'))->will($this->returnValue(true));
        $this->fileCache->expects($this->once())->method('get')
            ->with($this->equalTo('var1'))->will($this->returnValue('test'));
        $result = $this->cache->get('var1');
        $this->assertEquals('test', $result);
    }

    public function testGetCacheNotFound()
    {
        $this->staticCache->expects($this->once())->method('exists')
            ->with($this->equalTo('var1'))->will($this->returnValue(false));
        $this->fileCache->expects($this->once())->method('exists')
            ->with($this->equalTo('var1'))->will($this->returnValue(false));
        $this->expectException(CacheNotFoundException::class);
        $this->cache->get('var1');
    }

    public function testSetCache()
    {
        $data = [
            'key' => 'var1',
            'value' => 'var2',
            'ttl' => 123
        ];
        $this->staticCache->expects($this->once())->method('set')
            ->with($this->equalTo($data['key']), $this->equalTo($data['value']), $this->equalTo($data['ttl']));
        $this->fileCache->expects($this->once())->method('set')
            ->with($this->equalTo($data['key']), $this->equalTo($data['value']), $this->equalTo($data['ttl']));
        $this->cache->set($data['key'], $data['value'], $data['ttl']);
    }
}