<?php

namespace YameteTests\Driver;


class LoadLa extends \PHPUnit\Framework\TestCase
{
    public function testDownload()
    {
        $url = 'https://www.load.la/00/F1.t2._D4.5.j3.ns_G3.v2.-Y4.5.r-F3.rst-T3.m2.-t4.-M4.mmy-R1.3.k4.5.-7.ngl3.sh&Series=&num=12&type=&o=1530700341';
        $driver = new \Yamete\Driver\LoadLa();
        $driver->setUrl($url);
        $this->assertTrue($driver->canHandle());
        $this->assertEquals(20, count($driver->getDownloadables()));
    }
}
