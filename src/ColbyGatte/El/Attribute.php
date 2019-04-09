<?php

namespace ColbyGatte\El;

class Attribute
{
    use Sub;

    protected $tag;

    public function __construct($tag, $el)
    {
        $this->el = $el;
        $this->tag = $tag;
    }

    public function current()
    {
        return $this->tag;
    }

    public function __call($name, $arguments)
    {
        $this->tag->parseAttributes('', [$name => $arguments[0]]);

        return $this;
    }
}