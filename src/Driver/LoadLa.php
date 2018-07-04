<?php

namespace Yamete\Driver;

class LoadLa extends \Yamete\DriverAbstract
{
    private $aMatches = [];
    const DOMAIN = 'load.la';

    public function canHandle()
    {
        return (bool)preg_match(
            '~^https?://www\.(' . strtr(self::DOMAIN, ['.' => '\.']) . ')/00/(?<album>[^/]+)~',
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
        $aReturn = [];
        $oRes = $this->getClient()->request('GET', $this->sUrl);
        $i = 0;
        var_dump((string)$oRes->getBody());
        foreach ($this->getDomParser()->load((string)$oRes->getBody())->find('.pagination a') as $oLink) {
            /**
             * @var \PHPHtmlParser\Dom\AbstractNode $oLink
             */
            $oRes = $this->getClient()->request('GET', $this->sUrl);
            foreach ($this->getDomParser()->load((string)$oRes->getBody())->find('.search_gallery_item a') as $oLink) {
                /**
                 * @var \PHPHtmlParser\Dom\AbstractNode $oLink
                 */
                $oRes = $this->getClient()->request('GET', $oLink->getAttribute('href'));
                foreach ($this->getDomParser()->load((string)$oRes->getBody())->find('center a img') as $oImg) {
                    /**
                     * @var \PHPHtmlParser\Dom\AbstractNode $oLink
                     */
                    $sFilename = $oImg->getAttribute('src');
                    $sBasename = $this->getFolder() . DIRECTORY_SEPARATOR . str_pad(++$i, 5, '0', STR_PAD_LEFT)
                        . '-' . basename($sFilename);
                    $aReturn[$sBasename] = $sFilename;
                }
            }
        }

        return $aReturn;
    }

    public function getClient($aOptions = [])
    {
        return parent::getClient(['headers' => ['Referer' => $this->sUrl]]);
    }

    private function getFolder()
    {
        return implode(DIRECTORY_SEPARATOR, [self::DOMAIN, $this->aMatches['album']]);
    }
}
