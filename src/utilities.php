<?php

use ColbyGatte\El\Element;

function el($content, $do = null)
{
    $el = new Element($content);

    return $do ? $el->do($do) : $el;
}

function elsafe($content, $do = null)
{
    return el($content, $do)->safe();
}