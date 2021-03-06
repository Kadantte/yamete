<?php

namespace YameteTests\Driver;


use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;

class CartoonPornComicsInfo extends TestCase
{
    /**
     * @throws GuzzleException
     */
    public function testDownloadSecure()
    {
        $url = 'https://cartoonporncomics.info/bunnie-love-2-between-a-cock-and-a-hard-place-burnt-toast-media-comics/';
        $driver = new \Yamete\Driver\CartoonPornComicsInfo();
        $driver->setUrl($url);
        $this->assertTrue($driver->canHandle());
        $this->assertEquals(30, count($driver->getDownloadables()));
    }
}
