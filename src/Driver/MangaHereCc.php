<?php

namespace Yamete\Driver;

use GuzzleHttp\Client;
use iterator;
use PHPHtmlParser\Dom\AbstractNode;
use Yamete\DriverAbstract;

if (!class_exists(MangaHereCc::class)) {
    class MangaHereCc extends DriverAbstract
    {
        private $aMatches = [];
        private const DOMAIN = 'mangahere.cc';

        public function canHandle(): bool
        {
            return (bool)preg_match(
                '~^https?://(www\.)?(' . strtr($this->getDomain(), ['.' => '\.']) . ')/manga/(?<album>[^/]+)~',
                $this->sUrl,
                $this->aMatches
            );
        }

        /**
         * @return string
         */
        protected function getDomain(): string
        {
            return self::DOMAIN;
        }


        public function getDownloadables(): array
        {
            /**
             * @var iterator $oChapters
             * @var AbstractNode[] $aChapters
             * @var AbstractNode[] $oPages
             * @var AbstractNode $oImage
             */
            $sStartUrl = 'https://www.' . $this->getDomain();
            $sUrl = $sStartUrl . '/manga/' . $this->aMatches['album'] . '/';
            $oResponse = $this->getClient()->get($sUrl);
            $oChapters = $this->getDomParser()->load((string)$oResponse->getBody())->find('.detail-main-list li > a');
            $aChapters = iterator_to_array($oChapters);
            krsort($aChapters);
            $index = 0;
            $aReturn = [];
            foreach ($aChapters as $oLink) {
                $sCurrentUrl = $sStartUrl . $oLink->getAttribute('href');
                if (false === strpos($sCurrentUrl, '/1.html')) {
                    continue;
                }
                $oResponse = $this->getClient()->get($sCurrentUrl);
                $iPageCount = 0;
                $oPages = $this->getDomParser()->load((string)$oResponse->getBody())->find('.pager-list-left a');
                foreach ($oPages as $oPage) {
                    $iCurrentPage = $oPage->getAttribute('data-page');
                    $iPageCount = $iCurrentPage >= $iPageCount ? $iCurrentPage : $iPageCount;
                }
                for ($iCurrentPage = 1; $iCurrentPage <= $iPageCount; $iCurrentPage++) {
                    $oResponse = $this->getClient()->get(str_replace('/1.html', "/$iCurrentPage.html", $sCurrentUrl));
                    $oImage = $this->getDomParser()->load((string)$oResponse->getBody())->find('.reader-main-img')[0];
                    if (!$oImage) {
                        continue;
                    }
                    $sFilename = 'https:' . $oImage->getAttribute('src');
                    $sBasename = $this->getFolder() . DIRECTORY_SEPARATOR . str_pad($index++, 5, '0', STR_PAD_LEFT)
                        . '-' . basename($sFilename);
                    $aReturn[$sBasename] = $sFilename;
                }
            }
            return $aReturn;
        }

        private function getFolder(): string
        {
            return implode(DIRECTORY_SEPARATOR, [$this->getDomain(), $this->aMatches['album']]);
        }

        public function getClient(array $aOptions = []): Client
        {
            return parent::getClient(
                [
                    'headers' => ['Cookie' => 'isAdult=1'],
                ]
            );
        }
    }
}
