<?php

namespace ColbyGatte\El;

class Tag
{
    protected $tag;

    public function __construct($tag)
    {
        $this->tag = $tag;
    }

    public function __toString()
    {
        return $this->tag ? sprintf('</%s>', $this->tag) : '';
    }
}
