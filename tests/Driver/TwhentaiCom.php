<?php

namespace YameteTests\Driver;


use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;

class TwhentaiCom extends TestCase
{
    /**
     * @throws GuzzleException
     */
    public function testDownload()
    {
        $url = 'http://twhentai.com/hentai_doujin/71959/';
        $driver = new \Yamete\Driver\TwhentaiCom();
        $driver->setUrl($url);
        $this->assertTrue($driver->canHandle());
        $this->assertEquals(18, count($driver->getDownloadables()));
    }
}
