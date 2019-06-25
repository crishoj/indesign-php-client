<?php

namespace InDesignClient\Tests;

use InDesignClient\Client;
use InDesignClient\Exception\MalformedParametersException;

class ClientTest extends TestCase
{

    /** @var Client $instance */
    protected $instance;

    public function testExtends()
    {
        $this->assertInstanceOf('SoapClient', $this->instance);
    }

    public function testDefaultMethodCall()
    {
        $this->assertEmpty((array) $this->instance->doRunScript([
            "scriptText" => '',
        ]));
    }

    public function testMalformedExceptionIsThrown()
    {
        $this->expectException(MalformedParametersException::class);
        $this->instance->doRunScript([]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->instance = new Client($this->wsdl());
    }
}
