<?php

namespace InDesignClient\Tests;

use InDesignClient\Application;
use InDesignClient\Client;

class ApplicationTest extends TestCase
{
    /** @var Application $instance */
    protected $instance;

    public function testGetAllFonts()
    {
        $fonts = $this->instance->getAllFonts();
        $this->assertIsIterable($fonts);
        $this->assertGreaterThan(0, count($fonts));
    }

    public function testGetVersion()
    {
        $version = $this->instance->getVersion();
        $this->assertIsString($version);
        $this->assertGreaterThan(0, strlen($version));
    }

    public function testGetName()
    {
        $name = $this->instance->getName();
        $this->assertIsString($name);
        $this->assertGreaterThan(0, strlen($name));
    }

    public function testGetSerialNumber()
    {
        $serial = $this->instance->getSerialNumber();
        $this->assertIsString($serial);
        $this->assertGreaterThan(0, strlen($serial));
    }

    public function testSetUserName()
    {
        $name = "foo" . time();
        $return = $this->instance->setUserName($name);
        $this->assertTrue($return);
        $this->assertEquals($name, $this->instance->getUserName());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->instance = new Application(new Client($this->wsdl()));
    }
}
