<?php

namespace Yamete;

use GuzzleHttp\Client;

interface DriverInterface
{
    /**
     * Defines URL to be parsed
     * @param string $sUrl
     * @return $this
     */
    public function setUrl(string $sUrl): DriverInterface;

    /**
     * Tells if driver can handle defined URL (with setUrl)
     * @return bool
     */
    public function canHandle(): bool;

    /**
     * Returns URLs that can be downloaded (indexed by optional file name) for specified URL (with setUrl)
     * @return string[]
     */
    public function getDownloadables(): array;

    /**
     * Returns Guzzle client that will be used to download resources
     * @return Client
     */
    public function getClient(): Client;

    /**
     * Clean the current instance (free memory)
     * @return $this
     */
    public function clean(): DriverInterface;
}
