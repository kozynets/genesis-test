<?php declare(strict_types=1);

namespace genesis\cache\variants;

class FileVariantCache implements VariantCacheInterface
{
    /**
     * @var resource
     */
    private $fileName;

    /**
     * FileVariantCache constructor.
     * @param string $fileName
     */
    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        $cache = $this->getFileData();
        return $cache[$key]['value'];
    }

    /**
     * @param string $key
     * @param $value
     * @param int $ttl
     */
    public function set(string $key, $value, $ttl = 3600): void
    {
        $cache = $this->getFileData();
        $cache[$key] = [
            'value' => $value,
            'expiresAt' => time() + $ttl
        ];
        $this->saveFileData($cache);
        $this->getFileData();
    }

    /**
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool
    {
        $cache = $this->getFileData();

        return (isset($cache[$key]) && ($cache[$key]['expiresAt'] >= time()));
    }

    /**
     * @return array
     */
    private function getFileData(): array
    {
        $cache = unserialize(file_get_contents($this->fileName));

        return ($cache) ? $cache : [];
    }

    /**
     * @param array $cache
     */
    private function saveFileData(array $cache)
    {
        file_put_contents($this->fileName, serialize($cache));
    }

    public function __destruct()
    {
        $cache = $this->getFileData();
        foreach ($cache as $key => $cacheItem) {
            if ($cacheItem['expiresAt'] < time()) {
                unset($cache[$key]);
            }
        }
        $this->saveFileData($cache);
    }
}