<?php

namespace YameteTests\Driver;


use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;

class FreeFamousCartoonPornCom extends TestCase
{
    /**
     * @throws GuzzleException
     */
    public function testDownload()
    {
        $url = 'http://freefamouscartoonporn.com/content/bikini-girls-and-bikini-sex/index.html';
        $driver = new \Yamete\Driver\FreeFamousCartoonPornCom();
        $driver->setUrl($url);
        $this->assertTrue($driver->canHandle());
        $this->assertEquals(60, count($driver->getDownloadables()));
    }
}
