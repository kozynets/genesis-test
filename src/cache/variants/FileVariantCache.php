<?php declare(strict_types=1);

namespace genesis\cache\variants;

use genesis\exceptions\CacheDirectoryNotCreated;
use genesis\exceptions\CacheFileNotCreated;
use genesis\exceptions\CacheNotDefinedException;
use genesis\exceptions\CacheNotFoundException;

class FileVariantCache implements VariantCacheInterface
{
    /**
     * @var string
     */
    private $fileDirectoryPath;

    /**
     * @var string
     */
    private $fileSuffix = '.txt';

    /**
     * FileVariantCache constructor.
     * @param string $fileDirectoryPath
     */
    public function __construct(string $fileDirectoryPath)
    {
        $this->fileDirectoryPath = $fileDirectoryPath;
        if (!is_dir($this->fileDirectoryPath)) {
            throw new CacheDirectoryNotCreated('Cache directory not created');
        }
        chmod($this->fileDirectoryPath, 0775);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        $result = unserialize(file_get_contents($this->fileDirectoryPath . $key . $this->fileSuffix));
        if ($result == false) {
            throw new CacheNotFoundException('Cache not found');
        }

        return $result;
    }

    /**
     * @param string $key
     * @param $value
     * @param int $ttl
     */
    public function set(string $key, $value, int $ttl = 3600): void
    {
        $this->saveFileData($key, $value);
        $this->setExpiresAt($key, $ttl);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool
    {
        return filemtime($this->fileDirectoryPath . $key . $this->fileSuffix) >= time();
    }

    /**
     * @param string $key
     * @param int $ttl
     */
    private function setExpiresAt(string $key, int $ttl): void
    {
        if (!touch($this->fileDirectoryPath . $key . $this->fileSuffix, time() + $ttl)) {
            throw new CacheFileNotCreated('can\'t create cache file to key' . $key);
        }
    }

    /**
     * @param string $key
     * @param $value
     */
    private function saveFileData(string $key, $value)
    {
        if (!file_put_contents($this->fileDirectoryPath . $key . $this->fileSuffix, serialize($value))) {
            throw new CacheNotDefinedException('Can\'t define cache');
        }
    }

    public function __destruct()
    {
        foreach (scandir($this->fileDirectoryPath) as $file) {
            if (!in_array($file, ['.', '..']) && (filemtime($this->fileDirectoryPath . $file) < time())) {
                unlink($this->fileDirectoryPath . $file);
            }
        }
    }

}