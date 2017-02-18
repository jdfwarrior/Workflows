<?php

namespace AlfredApp;

class Cache
{
    const PATH_CACHE = "/Library/Caches/com.runningwithcrayons.Alfred-%d/Workflow Data/";

    /**
     * @var string
     */
    private $cachePath;

    /**
     * @var string
     */
    private $bundleId;

    /**
     * @param string $bundleId
     * @param int $versionNumber
     */
    public function __construct($bundleId, $versionNumber)
    {
        $this->bundleId = $bundleId;
        $this->version = $versionNumber;
    }

    /**
     * @return boolean
     */
    private function setupCachePath()
    {
        if ($this->bundleId) {
            $this->cachePath = sprintf($_SERVER['HOME'] . self::PATH_CACHE . $this->bundleId, $this->version);
            if (!file_exists($this->cachePath)) {
                return mkdir($this->cachePath);
            }
        }
        return false;
    }
}