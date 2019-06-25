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

    private $port = 12345;
    private $ip = '127.0.0.1';

    /** @var Application $application */
    private $application = null;

    function __construct(string $wsdl = 'http://127.0.0.1:12345/service?wsdl')
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
     * @throws ApiCallException
     * @throws Exception\MalformedParametersException
     */
    function simpleRunScript(string $script, array $parameters = []): stdClass
    {
        return $this->doRunScript([
            'scriptText'        => $script,
            'script_parameters' => $parameters
        ]);
    }

    /**
     * Call main function of Indesign Server API
     *
     * @throws ApiCallException
     * @throws Exception\MalformedParametersException
     */
    function doRunScript(array $scriptParameters): ?stdClass
    {
        if (! array_key_exists('scriptLanguage', $scriptParameters)) {
            $scriptParameters['scriptLanguage'] = $this->scriptLanguage;
        }

        $this->assertValidScriptParameters($scriptParameters);

        $return = $this->RunScript(["runScriptParameters" => $scriptParameters]);
        return is_object($return)
            ? self::getReturnValues($return)
            : null;
    }

    /**
     * @throws Exception\MalformedParametersException
     */
    private function assertValidScriptParameters(array $scriptParameters)
    {
        if (! array_key_exists('scriptLanguage', $scriptParameters)) {
            throw new Exception\MalformedParametersException('scriptLanguage');
        }

        if (! array_key_exists('scriptText', $scriptParameters) and ! array_key_exists('scriptFile',
                $scriptParameters)) {
            throw new Exception\MalformedParametersException(['scriptText', 'scriptFile']);
        }
    }

    /**
     * @throws Exception\ApiCallException
     */
    public static function getReturnValues(stdClass $obj): stdClass
    {
        if ($obj->errorNumber == 0) {
            return $obj->scriptResult;
        } else {
            $code = $obj->errorNumber;
            $message = $obj->errorString;

            throw new ApiCallException($message, $code);
        }
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;
        return $this;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function setPort(int $port): self
    {
        $this->port = $port;
        return $this;
    }

    public function getScriptLanguage(): string
    {
        return $this->scriptLanguage;
    }

    public function setScriptLanguage(string $scriptLanguage): self
    {
        $this->scriptLanguage = $scriptLanguage;
        return $this;
    }

    public function getApplication(): Application
    {
        // Memoize
        return $this->application
            ?? $this->application = new Application($this);
    }

}
