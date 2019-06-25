<?php

namespace InDesignClient;

use InDesignClient\Exception\ApiCallException;
use SoapClient;
use stdClass;

/**
 * Client
 *
 * @package    IndesignClient
 * @author     david ribes <ribes.david@gmail.com>
 */
class Client extends SoapClient
{

    private $scriptLanguage = 'javascript';

    private $port = '12345';
    private $ip = '127.0.0.1';

    /** @var Application $application */
    private $application = null;

    function __construct($wsdl = 'http://127.0.0.1:12345/service?wsdl')
    {
        preg_match('/[0-9.]+/', $wsdl, $ipMatches);
        preg_match('/:[0-9]+/', $wsdl, $portMatches);

        $this->setIp($ipMatches[0]);
        $this->setPort(str_replace(":", "", $portMatches[0]));

        $this->SoapClient($wsdl, [
            'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
            'proxy_port'  => $this->port,
            'proxy_host'  => $ipMatches[0],
        ]);
    }

    /**
     * @param       $script
     * @param array $parameters
     * @return array
     * @throws ApiCallException
     * @throws Exception\MalformedParametersException
     */
    function simpleRunScript($script, $parameters = [])
    {
        return $this->doRunScript([
            'scriptText'        => $script,
            'script_parameters' => $parameters
        ]);
    }

    /**
     * Call main function of Indesign Server API
     * @param array $scriptParameters
     * @return array
     * @throws ApiCallException
     * @throws Exception\MalformedParametersException
     */
    function doRunScript(array $scriptParameters)
    {
        if (! array_key_exists('scriptLanguage', $scriptParameters)) {
            $scriptParameters['scriptLanguage'] = $this->scriptLanguage;
        }

        if ($this->validScriptParameters($scriptParameters)) {
            $return = $this->RunScript(["runScriptParameters" => $scriptParameters]);
            if (is_object($return)) {
                return self::getReturnValues($return);
            }
        }
        return null;
    }

    /**
     * @param $scriptParameters
     * @return bool
     * @throws Exception\MalformedParametersException
     */
    private function validScriptParameters($scriptParameters)
    {
        if (! array_key_exists('scriptLanguage', $scriptParameters)) {
            throw new Exception\MalformedParametersException('scriptLanguage');
        }

        if (! array_key_exists('scriptText', $scriptParameters) and ! array_key_exists('scriptFile',
                $scriptParameters)) {
            throw new Exception\MalformedParametersException(['scriptText', 'scriptFile']);
        }

        return true;
    }

    /**
     * @param stdClass $obj
     * @return array
     * @throws Exception\ApiCallException
     */
    public static function getReturnValues(stdClass $obj)
    {
        if ($obj->errorNumber == 0) {
            return $obj->scriptResult;
        } else {
            $code = $obj->errorNumber;
            $message = $obj->errorString;

            throw new ApiCallException($message, $code);
        }
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     * @return $this
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param string $port
     * @return $this
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @return string
     */
    public function getScriptLanguage()
    {
        return $this->scriptLanguage;
    }

    /**
     * @param string $scriptLanguage
     * @return $this
     */
    public function setScriptLanguage($scriptLanguage)
    {
        $this->scriptLanguage = $scriptLanguage;
        return $this;
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        if (is_object($this->application)) {
            return $this->application;
        }
        $this->application = new Application($this);
        return $this->application;
    }


}
