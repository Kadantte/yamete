<?php

namespace YameteTests\Driver;


use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;

class Hentai2read extends TestCase
{
    /**
     * @throws GuzzleException
     */
    public function testDownload()
    {
        $url = 'https://hentai2read.com/clumsy_girl/';
        $driver = new \Yamete\Driver\Hentai2read();
        $driver->setUrl($url);
        $this->assertTrue($driver->canHandle());
        $this->assertEquals(26, count($driver->getDownloadables()));
    }

    /**
     * @throws GuzzleException
     */
    public function testDownloadCollection()
    {
        $url = 'https://hentai2read.com/y_mountain_villas_lineage/';
        $driver = new \Yamete\Driver\Hentai2read();
        $driver->setUrl($url);
        $this->assertTrue($driver->canHandle());
        $this->assertEquals(47, count($driver->getDownloadables()));
    }
}
