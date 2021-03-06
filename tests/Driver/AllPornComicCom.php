<?php

namespace YameteTests\Driver;


use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;

class AllPornComicCom extends TestCase
{
    /**
     * @throws GuzzleException
     */
    public function testDownload()
    {
        $url = 'https://allporncomic.com/porncomic/aunts-juice-mothers-juice-nishikawa-kou/1-aunts-juice-mothers-juice/';
        $driver = new \Yamete\Driver\AllPornComicCom();
        $driver->setUrl($url);
        $this->assertTrue($driver->canHandle());
        $this->assertEquals(42, count($driver->getDownloadables()));
    }
}
