<?php declare(strict_types=1);

namespace genesis\cache;

interface CacheInterface
{
    public function get(string $key);

    public function set(string $key, $value, int $ttl = 3600);
}
