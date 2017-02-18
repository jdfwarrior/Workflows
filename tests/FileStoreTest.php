<?php

namespace AlfredAppTest;

use AlfredApp\Exceptions\FileStoreException;
use AlfredApp\FileStore;

class FileStoreTest extends \PHPUnit_Framework_TestCase
{
    const DIRECTORY_TESTS = 'tests' . DIRECTORY_SEPARATOR;
    const DIRECTORY_NEW = self::DIRECTORY_TESTS . 'newDirectory';
    const DIRECTORY_FIXTURES = self::DIRECTORY_TESTS . 'fixtures';

    const FILE_GOOD = 'fileStoreGood.json';
    const FILE_BAD = 'fileStoreBad.json';
    const FILE_I_DONT_EXIST = 'iDontExist';
    const FILE_WRITE = 'fileStoreWriteTest';

    public function testConstruct_PathExists()
    {
        $fileStore = new FileStore(self::DIRECTORY_FIXTURES);
        $this->assertInstanceOf(FileStore::class, $fileStore);
    }

    public function testConstruct_PathNotExists()
    {
        $fileStore = new FileStore(self::DIRECTORY_NEW);
        $this->assertInstanceOf(FileStore::class, $fileStore);
        $this->assertTrue(file_exists(self::DIRECTORY_NEW));
    }

    public function testExists_True()
    {
        $fileStore = new FileStore(self::DIRECTORY_FIXTURES);
        $this->assertTrue($fileStore->exists(self::FILE_GOOD));
    }

    public function testExists_False()
    {
        $fileStore = new FileStore(self::DIRECTORY_FIXTURES);
        $this->assertFalse($fileStore->exists(self::FILE_I_DONT_EXIST));
    }

    public function testRead_FileNotExists_throwsException()
    {
        $fileStore = new FileStore(self::DIRECTORY_FIXTURES);
        $this->expectException(FileStoreException::class);
        $fileStore->read(self::FILE_I_DONT_EXIST);
    }

    public function testRead_BadJson_throwsException()
    {
        $fileStore = new FileStore(self::DIRECTORY_FIXTURES);
        $this->expectException(FileStoreException::class);
        $fileStore->read(self::FILE_BAD);
    }

    public function testRead_AsObject()
    {
        $fileStore = new FileStore(self::DIRECTORY_FIXTURES);
        $result = $fileStore->read(self::FILE_GOOD, false);
        $this->assertInstanceOf(\stdClass::class, $result);
        $this->assertEquals('world', $result->hello);
    }

    public function testRead_NotAsObject()
    {
        $fileStore = new FileStore(self::DIRECTORY_FIXTURES);
        $result = $fileStore->read(self::FILE_GOOD, true);
        $this->assertInternalType('array', $result);
        $this->assertSame(['hello' => 'world'], $result);
    }

    public function testWrite()
    {
        $fileStore = new FileStore(self::DIRECTORY_FIXTURES);
        $result = $fileStore->write(self::FILE_WRITE, [1,2,3]);

        $fullPath = self::DIRECTORY_FIXTURES . DIRECTORY_SEPARATOR . self::FILE_WRITE;

        $this->assertTrue($result);
        $this->assertTrue(file_exists($fullPath));
        $this->assertSame('[1,2,3]', file_get_contents($fullPath));
    }

    public function tearDown()
    {
        if(file_exists(self::DIRECTORY_NEW)) {
            rmdir(self::DIRECTORY_NEW);
        }

        if(file_exists(self::DIRECTORY_FIXTURES . DIRECTORY_SEPARATOR . self::FILE_WRITE)) {
            unlink(self::DIRECTORY_FIXTURES . DIRECTORY_SEPARATOR . self::FILE_WRITE);
        }
    }
}