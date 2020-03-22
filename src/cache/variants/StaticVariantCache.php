<?php declare(strict_types=1);

namespace genesis\cache\variants;

class StaticVariantCache implements VariantCacheInterface
{
    /**
     * @var array
     */
    private static $cache;

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        return self::$cache[$key]['value'];
    }

    /**
     * @param string $key
     * @param $value
     * @param int $ttl
     */
    public function set(string $key, $value, $ttl = 3600)
    {
        self::$cache[$key] = [
            'value' => $value,
            'expiresAt' => \time() + $ttl
        ];
    }

    /**
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool
    {
        return (isset(self::$cache[$key]) && (self::$cache[$key]['expiresAt'] >= time()));
    }
}