<?php

namespace Yamete\Driver;

class WarcraftPornPro extends XXXComicPornCom
{
    private const DOMAIN = 'warcraftporn.pro';

    protected function getDomain(): string
    {
        return self::DOMAIN;
    }
}
