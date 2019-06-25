<?php


namespace IndesignClient\Tests;


class TestCase extends \PHPUnit\Framework\TestCase
{

    /**
     * @return string
     */
    protected function wsdl(): string
    {
        $host = getenv('INDESIGN_HOST');
        $port = getenv('INDESIGN_PORT');
        $wsdl = "http://{$host}:{$port}/service?wsdl";
        return $wsdl;
    }
}
