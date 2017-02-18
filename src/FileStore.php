<?php

namespace AlfredApp;

use AlfredApp\Exceptions\FileStoreException;

class FileStore
{
    /**
     * @var string
     */
    private $path;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
        $this->initPath();
    }

    /**
     * @param string|array $filename - filename to write the cache data to
     * @param array $data - data to save to file
     * @return bool
     */
    public function write($filename, array $data)
    {
        $data = json_encode($data);
        return $this->atomicFilePutContents($filename, $data);
    }

    /**
     * Returns file from the filestore
     *
     * @param string $filename filename to read the cache data from
     * @param bool $returnAsAssoc
     * @return \stdClass|array
     * @throws FileStoreException if file does not exist or JSON is invalid
     */
    public function read($filename, $returnAsAssoc = false)
    {
        if (!$this->exists($filename)) {
            throw new FileStoreException(sprintf("Unable to find file %s", $this->getFullPath($filename)));
        }

        $contents = file_get_contents($this->getFullPath($filename));
        $decoded = json_decode($contents, $returnAsAssoc);

        if (is_null($decoded)) {
            throw new FileStoreException(sprintf("Invalid JSON in file %s", $this->getFullPath($filename)));
        }

        return $decoded;

    }

    /**
     * @param string $filename
     * @return bool
     */
    public function exists($filename)
    {
        return file_exists($this->getFullPath($filename));
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $filename
     * @return string
     */
    public function getFullPath($filename)
    {
        return $this->path . DIRECTORY_SEPARATOR . $filename;
    }

    /**
     * @param string $filename
     * @param mixed $data
     * @return bool
     */
    private function atomicFilePutContents($filename, $data)
    {
        // Perform an exclusive (locked) overwrite to a temporary file.
        $temporaryFilename = sprintf( '%s.atomictmp', $filename );
        $writeResult = file_put_contents( sys_get_temp_dir() . $temporaryFilename, $data, LOCK_EX );
        if( $writeResult === false ) {
            return false;
        }

        // Now move the file to its real destination (replaced if exists).
        $moveResult = rename( sys_get_temp_dir() . $temporaryFilename, $this->getFullPath($filename) );
        if( $moveResult === false ) {
            return false;
        }

        return true;
    }

    /**
     * @return boolean
     */
    private function initPath()
    {
        if (!file_exists($this->path)) {
            return mkdir($this->path);
        }
        return false;
    }
}