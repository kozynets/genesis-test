<?php declare(strict_types=1);

namespace genesis\cache\variants;

use genesis\cache\CacheInterface;

interface VariantCacheInterface extends CacheInterface
{
    public function exists(string $key): bool;
}