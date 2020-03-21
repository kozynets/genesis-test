<?php declare(strict_types=1);

namespace genesis\cache;

use genesis\cache\variants\VariantCacheInterface;
use genesis\exceptions\CacheNotFoundException;

class Cache implements CacheInterface
{
    /**
     * @var VariantCacheInterface[]
     */
    private $cacheVariants;

    /**
     * Cache constructor.
     * @param VariantCacheInterface ...$cacheVariants
     */
    public function __construct(VariantCacheInterface...$cacheVariants)
    {
        $this->cacheVariants = $cacheVariants;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        foreach ($this->cacheVariants as $cache) {
            if ($cache->exists($key)) {
                return $cache->get($key);
            }
        }

        throw new CacheNotFoundException('Cache not defined for key ' . $key);
    }

    /**
     * @param string $key
     * @param $value
     * @param int $ttl
     */
    public function set(string $key, $value, $ttl = 3600)
    {
        foreach ($this->cacheVariants as $cache) {
            $cache->set($key, $value, $ttl);
        }
    }
}