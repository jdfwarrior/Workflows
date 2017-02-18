<?php

namespace AlfredAppTest;

require_once 'vendor/autoload.php';

use AlfredApp\FileStore;
use AlfredApp\Workflows;

class WorkflowsTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct_NoId()
    {
        $workflow = new Workflows();
        $this->assertFalse($workflow->bundle());
        $this->assertNull($workflow->cache());
        $this->assertNull($workflow->data());
    }

    public function testConstruct_GetIdFromPlist()
    {
        // Copy the file to cwd because that's where the class looks for it
        $this->copyPlistIntoCwd();

        $workflow = new Workflows();
        $this->assertSame('someBundleIdInXML', $workflow->bundle());
    }

    public function testBundle_NoId_ReturnFalse()
    {
        $workflow = new Workflows();
        $this->assertFalse($workflow->bundle());
    }

    public function testBundle_WithId_ReturnId()
    {
        $workflow = new Workflows(1234);
        $this->assertSame(1234, $workflow->bundle());
    }

    public function testCache_NoId_ReturnsNull()
    {
        $workflow = new Workflows();
        $this->assertNull($workflow->cache());
    }

    public function testCache_WithId_ReturnsPath()
    {
        $workflow = new Workflows(1234);
        $cacheStore = $workflow->cache();
        $this->assertInstanceOf(FileStore::class, $cacheStore);
        $this->assertStringEndsWith((string) 1234, $cacheStore->getPath());
    }

    public function testData_NoId_ReturnsNull()
    {
        $workflow = new Workflows();
        $this->assertNull($workflow->data());
    }

    public function testData_WithId_ReturnsPath()
    {
        $workflow = new Workflows(1234);
        $dataStore = $workflow->data();
        $this->assertInstanceOf(FileStore::class, $dataStore);
        $this->assertStringEndsWith((string) 1234, $dataStore->getPath());
    }

    public function testPath_ReturnsPwd()
    {
        $workflow = new Workflows();
        $this->assertNotNull($workflow->path());
        $this->assertInstanceOf(FileStore::class, $workflow->path());
        $this->assertEquals(getcwd(), $workflow->path()->getPath());
    }

    public function testToXml()
    {
        $this->markTestIncomplete("Very difficult to test in current state");
    }

    public function testSet()
    {
        $this->markTestIncomplete("Very difficult to test in current state");
    }

    public function testGet_badPath_throwsException()
    {
        $w = new Workflows();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Unable to determine fullPath for someFileName");
        $w->get('someFileName', 'someProperty');
    }

    public function testGet_GoodPathMissingProperty_returnsFalse()
    {
        $w = new Workflows();
        $this->copyPlistIntoCwd();
        $actual = $w->get(Workflows::INFO_PLIST, 'missingProperty');

        $this->assertFalse($actual);
    }

    public function testGet_GoodPathWithProperty_returnsPropertyValue()
    {
        $w = new Workflows();
        $this->copyPlistIntoCwd();
        $actual = $w->get(Workflows::INFO_PLIST, 'myTestProperty');

        $this->assertSame('testPropertyValue', $actual);
    }

    public function testGet()
    {
        $this->markTestIncomplete("Need to test the various path tries");
    }

    public function tearDown()
    {
        parent::tearDown();
        if (file_exists($this->getInfoPlistPath())) {
            unlink($this->getInfoPlistPath());
        }
    }

    /**
     * @return string
     */
    private function getInfoPlistPath()
    {
        return getcwd() . '/' . Workflows::INFO_PLIST;
    }

    /**
     * @return void
     */
    private function copyPlistIntoCwd()
    {
        $plistFixtureFile = __DIR__.'/fixtures/'.Workflows::INFO_PLIST;
        $this->assertTrue(file_exists($plistFixtureFile), "plist fixture is missing");

        copy($plistFixtureFile, $this->getInfoPlistPath());
    }
}