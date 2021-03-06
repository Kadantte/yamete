<?php

namespace YameteTests\Driver;


use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;

class SexComixMe extends TestCase
{
    /**
     * @throws GuzzleException
     */
    public function testDownload()
    {
        $url = 'http://www.sexcomix.me/galleries/ben-10-turning-it-up-to-11-jlullaby#&gid=1&pid=1';
        $driver = new \Yamete\Driver\SexComixMe();
        $driver->setUrl($url);
        $this->assertTrue($driver->canHandle());
        $this->assertEquals(4, count($driver->getDownloadables()));
    }
}
