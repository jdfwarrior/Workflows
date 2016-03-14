<?php

namespace AlfredAppTest;

require_once 'vendor/autoload.php';

use AlfredApp\Workflows;

class WorkflowsTest extends \PHPUnit_Framework_TestCase
{
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
    }

    public function testHome()
    {
        $workflow = new Workflows();
        $this->assertNotNull($workflow->home());
        $this->assertSame($_SERVER['HOME'], $workflow->home());
    }

}