<?php

namespace AlfredAppTest;

require_once 'vendor/autoload.php';

use AlfredApp\Workflows;

class WorkflowsTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct_NoId()
    {
        $workflow = new Workflows();
        $this->assertFalse($workflow->bundle());
        $this->assertFalse($workflow->cache());
        $this->assertFalse($workflow->data());
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

    public function testCache_NoId_ReturnsFalse()
    {
        $workflow = new Workflows();
        $this->assertFalse($workflow->cache());
    }

    public function testCache_WithId_ReturnsPath()
    {
        $workflow = new Workflows(1234);
        $cachePath = $workflow->cache();
        $this->assertNotNull($cachePath);
        $this->assertStringEndsWith((string) 1234, $cachePath);
    }

    public function testData_NoId_ReturnsFalse()
    {
        $workflow = new Workflows();
        $this->assertFalse($workflow->data());
    }

    public function testData_WithId_ReturnsPath()
    {
        $workflow = new Workflows(1234);
        $dataPath = $workflow->data();
        $this->assertNotNull($dataPath);
        $this->assertStringEndsWith((string) 1234, $dataPath);
    }

    public function testPath_ReturnsPwd()
    {
        $workflow = new Workflows();
        $this->assertNotNull($workflow->path());
        $this->assertEquals(getcwd(), $workflow->path());
    }

    public function testHome()
    {
        $workflow = new Workflows();
        $this->assertNotNull($workflow->home());
        $this->assertSame($_SERVER['HOME'], $workflow->home());
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