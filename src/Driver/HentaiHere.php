<?php

namespace Yamete\Driver;

use GuzzleHttp\Exception\GuzzleException;
use PHPHtmlParser\Dom\AbstractNode;
use Yamete\DriverAbstract;

class HentaiHere extends DriverAbstract
{
    private $aMatches = [];
    private const DOMAIN = 'hentaihere.com';

    public function canHandle(): bool
    {
        return (bool)preg_match(
            '~^https?://(' . strtr(self::DOMAIN, ['.' => '\.']) . ')/m/(?<album>[^/]+)~',
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
        $this->sUrl = "https://" . self::DOMAIN . '/m/' . $this->aMatches['album'];
        $aReturn = [];
        $oRes = $this->getClient()->request('GET', $this->sUrl . '/1/1/');
        $iNbChapter = count($this->getDomParser()->load((string)$oRes->getBody())->find('.dropdown ul.text-left li'));
        $index = 0;
        for ($iChapter = 1; $iChapter <= $iNbChapter; $iChapter++) {
            $oRes = $this->getClient()->request('GET', $this->sUrl . '/' . $iChapter . '/1/');
            $sRegExp = '~<a href="https://' . self::DOMAIN . '/report/(?<chapterName>[^"]+)">~';
            $aMatch = [];
            if (!preg_match($sRegExp, (string)$oRes->getBody(), $aMatch)) {
                continue;
            }
            $sThumbsUrl = str_replace('/m/', '/thumbs/', $this->sUrl) . '/' . $aMatch['chapterName'];
            $oRes = $this->getClient()->request('GET', $sThumbsUrl);
            foreach ($this->getDomParser()->load((string)$oRes->getBody())->find('.item img') as $oThumb) {
                /* @var AbstractNode $oThumb */
                $sFilename = strtr($oThumb->getAttribute('src'), ['/thumbnails' => '', 'tmb' => '']);
                $sBasename = $this->getFolder() . DIRECTORY_SEPARATOR . str_pad(++$index, 5, '0', STR_PAD_LEFT)
                    . '-' . basename($sFilename);
                $aReturn[$sBasename] = $sFilename;
            }
        }

        return $aReturn;
    }

    private function getFolder(): string
    {
        return implode(DIRECTORY_SEPARATOR, [self::DOMAIN, $this->aMatches['album']]);
    }
}
