<?php

namespace YameteTests\Driver;


use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;

class PornGamesZone extends TestCase
{
    /**
     * @throws GuzzleException
     */
    public function testDownload()
    {
        $url = 'https://porngames.zone/e5aa613e7de1edc10e1e7da103cfb48b-honey-deep';
        $driver = new \Yamete\Driver\PornGamesZone();
        $driver->setUrl($url);
        $this->assertTrue($driver->canHandle());
        $this->assertEquals(30, count($driver->getDownloadables()));
    }
}
