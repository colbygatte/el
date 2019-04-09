<?php

namespace ColbyGatte\El;

class Helper
{
    public static function unkey($array)
    {
        return [array_keys($array), array_values($array)];
    }
}