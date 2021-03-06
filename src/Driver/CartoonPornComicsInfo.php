<?php

namespace Yamete\Driver;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use PHPHtmlParser\Dom\AbstractNode;
use Yamete\DriverAbstract;

class CartoonPornComicsInfo extends DriverAbstract
{
    private $aMatches = [];
    private const DOMAIN = 'cartoonporncomics.info';

    public function canHandle(): bool
    {
        return (bool)preg_match(
            '~^https?://(' . strtr(self::DOMAIN, ['.' => '\.']) . ')/(?<album>[^/]+)/$~',
            $this->sUrl,
            $this->aMatches
        );
    }

    /**
     * @return array|string[]
     * @throws GuzzleException
     */
    public function getDownloadables(): array
    {
        $oRes = $this->getClient()->request('GET', $this->sUrl);
        $aReturn = [];
        $aMatches = [];
        $index = 0;
        foreach ($this->getDomParser()->load((string)$oRes->getBody())->find('.my-gallery figure a') as $oLink) {
            /**
             * @var AbstractNode $oLink
             * @var AbstractNode $oImg
             */
            $oRes = $this->getClient()->request('GET', html_entity_decode($oLink->getAttribute('href')));
            if (preg_match('~"([^"]+bigImages[^"]+)"~', (string)$oRes->getBody(), $aMatches) === false) {
                continue;
            }
            $sFilename = $aMatches[1];
            $sBasename = $this->getFolder() . DIRECTORY_SEPARATOR . str_pad($index++, 5, '0', STR_PAD_LEFT)
                . '-' . basename($sFilename);
            $aReturn[$sBasename] = $sFilename;
        }
        return $aReturn;
    }

    private function getFolder(): string
    {
        return implode(DIRECTORY_SEPARATOR, [self::DOMAIN, $this->aMatches['album']]);
    }

    /**
     * @param array $aOptions
     * @return Client
     */
    public function getClient(array $aOptions = []): Client
    {
        return parent::getClient(['headers' => ['User-Agent' => self::USER_AGENT], 'http_errors' => false]);
    }
}
