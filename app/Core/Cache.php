<?php

namespace App\Core;

class Cache {
    private $cacheDir;

    public function __construct() {
        $this->cacheDir = ROOT_PATH . '/cache/';
        if (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0777, true);
        }
    }

    /**
     * Get cached data
     * SECURITY: Uses json_decode instead of unserialize to prevent PHP object injection
     * 
     * @param string $key Unique key for the data
     * @param int $ttl Time to live in seconds (default 3600 = 1 hour)
     * @return mixed|false The cached data or false if expired/not found
     */
    public function get($key, $ttl = 3600) {
        $filename = $this->getFilename($key);
        
        if (!file_exists($filename)) {
            return false;
        }

        // Check if expired
        if (time() - filemtime($filename) > $ttl) {
            @unlink($filename);
            return false;
        }

        $content = file_get_contents($filename);
        if ($content === false) {
            return false;
        }

        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("Cache deserialization error: " . json_last_error_msg());
            return false;
        }

        return $data;
    }

    /**
     * Set cached data
     * SECURITY: Uses json_encode instead of serialize to prevent PHP object injection
     * 
     * @param string $key Unique key
     * @param mixed $data Data to store
     * @return bool Success or failure
     */
    public function set($key, $data) {
        if (!is_writable($this->cacheDir)) return false;
        $filename = $this->getFilename($key);
        $encoded = json_encode($data);
        if ($encoded === false) {
            error_log("Cache encoding error: " . json_last_error_msg());
            return false;
        }
        $result = @file_put_contents($filename, $encoded);
        return $result !== false;
    }

    /**
     * Delete cached item
     */
    public function delete($key) {
        $filename = $this->getFilename($key);
        if (file_exists($filename)) {
            return unlink($filename);
        }
        return true;
    }

    private function getFilename($key) {
        return $this->cacheDir . md5($key) . '.cache';
    }
}
