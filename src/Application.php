<?php

namespace InDesignClient;

class Application
{

    /** @var Client $client */
    private $client;

    function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get fonts installed on the server
     */
    public function getAllFonts(): array
    {
        $return = $this->client->simpleRunScript('app.fonts');

        $fonts = [];

        foreach ($return->data->item as $id => $item) {
            $fonts[$id] = $item->specifierData;
        }

        return $fonts;
    }

    /**
     * Get server version
     */
    public function getVersion(): string
    {
        $return = $this->client->simpleRunScript('app.version');

        return (string) $return->data;
    }

    /**
     * Get the name of the application
     */
    public function getName(): string
    {
        $return = $this->client->simpleRunScript('app.name');

        return (string) $return->data;
    }

    /**
     * Get user serial number
     */
    public function getSerialNumber(): string
    {
        $return = $this->client->simpleRunScript('app.serialNumber');

        return (string) $return->data;
    }

    /**
     * Get the user associated with the tracked changes and notes.
     */
    public function getUserName(): string
    {
        $return = $this->client->simpleRunScript('app.userName');

        return (string) $return->data;
    }

    public function setUserName($userName): bool
    {
        $return = $this->client->simpleRunScript('app.userName = "' . $userName . '"');

        return ($userName === $return->data);
    }

}
