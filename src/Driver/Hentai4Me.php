<?php

namespace Yamete\Driver;

use Tuna\CloudflareMiddleware;

class Hentai4Me extends \Yamete\DriverAbstract
{
    private $aMatches = [];
    const DOMAIN = 'hentai4me.net';


    protected function getDomain()
    {
        return self::DOMAIN;
    }

    public function canHandle()
    {
        return (bool)preg_match(
            '~^https?://(' . strtr($this->getDomain(), ['.' => '\.']) . ')/(?<album>[^/]+)\.html$~',
            $this->sUrl,
            $this->aMatches
        );
    }

    /**
     * @return array|string[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDownloadables()
    {
        $oRes = $this->getClient()->request('GET', $this->sUrl);
        $aReturn = [];
        $i = 0;
        foreach ($this->getDomParser()->load((string)$oRes->getBody())->find('.chap-content p img') as $oImg) {
            /**
             * @var \PHPHtmlParser\Dom\AbstractNode $oImg
             */
            $sFilename = $oImg->getAttribute('data-lazy-src');
            if (!$sFilename) {
                continue;
            }
            $sBasename = $this->getFolder() . DIRECTORY_SEPARATOR . str_pad($i++, 5, '0', STR_PAD_LEFT)
                . '-' . basename($sFilename);
            $aReturn[$sBasename] = $sFilename;
        }
        return $aReturn;
    }

    /**
     * @param array $aOptions
     * @return \GuzzleHttp\Client
     */
    public function getClient($aOptions = [])
    {
        $oClient = parent::getClient(
            [
                'cookies' => new \GuzzleHttp\Cookie\FileCookieJar(tempnam('/tmp', __CLASS__)),
                'headers' => ['User-Agent' => self::USER_AGENT, 'Referer' => $this->sUrl],
            ]
        );
        /**
         * @var \GuzzleHttp\HandlerStack $oHandler
         */
        $oHandler = $oClient->getConfig('handler');
        $oHandler->push(CloudflareMiddleware::create());
        return $oClient;
    }

    private function getFolder()
    {
        return implode(DIRECTORY_SEPARATOR, [$this->getDomain(), $this->aMatches['album']]);
    }
}
