<?php

namespace Yamete\Driver;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use iterator;
use PHPHtmlParser\Dom\AbstractNode;
use Yamete\DriverAbstract;


if (!class_exists(IsekaiScanCom::class)) {
    class IsekaiScanCom extends DriverAbstract
    {
        protected $aMatches = [];
        private const DOMAIN = 'isekaiscan.com';

        public function canHandle(): bool
        {
            return (bool)preg_match(
                '~^https?://(' . strtr($this->getDomain(), ['.' => '\.']) . ')/manga/(?<album>[^/]+)~',
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

        /**
         * Where to download
         * @return string
         */
        protected function getFolder(): string
        {
            return implode(DIRECTORY_SEPARATOR, [$this->getDomain(), $this->aMatches['album']]);
        }

        /**
         * Rule to get all chapters links
         * @return string
         */
        protected function getChapterRule(): string
        {
            return '.chapter a';
        }

        /**
         * @return array|string[]
         * @throws GuzzleException
         */
        public function getDownloadables(): array
        {
            /**
             * @var iterator $oChapters
             * @var AbstractNode[] $aChapters
             * @var AbstractNode[] $oPages
             */
            $sUrl = 'https://' . $this->getDomain() . '/manga/' . $this->aMatches['album'] . '/';
            $oResult = $this->getClient()->request('GET', $sUrl);
            $oChapters = $this->getDomParser()->load((string)$oResult->getBody())->find($this->getChapterRule());
            $aChapters = iterator_to_array($oChapters);
            krsort($aChapters);
            $aReturn = [];
            $index = 0;
            foreach ($aChapters as $oChapter) {
                $oResult = $this->getClient()->request('GET', $oChapter->getAttribute('href'));
                $aMatches = [];
                if (!preg_match_all($this->getRegexp(), (string)$oResult->getBody(), $aMatches)) {
                    continue;
                }
                foreach ($aMatches[1] as $sFilename) {
                    $sFilename = trim($sFilename);
                    $sBasename = $this->getFolder() . DIRECTORY_SEPARATOR . str_pad($index++, 5, '0', STR_PAD_LEFT)
                        . '-' . basename($sFilename);
                    $aReturn[$sBasename] = $sFilename;
                }
            }
            return $aReturn;
        }

        protected function getRegexp(): string
        {
            return '~data-src="([^"]+)" class="wp-manga~';
        }

        public function getClient(array $aOptions = []): Client
        {
            return parent::getClient(['headers' => ['Referer' => $this->sUrl]]);
        }
    }
}
