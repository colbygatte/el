<?php

namespace ColbyGatte\El;

trait Sub
{
    /**
     * @var \ColbyGatte\El\Element
     */
    protected $el;

    /**
     * @return \ColbyGatte\El\Element
     */
    public function top()
    {
        return $this->el->getTop();
    }

    public function str()
    {
        return $this->el->str();
    }

    public function __toString()
    {
        return $this->str();
    }

    public function __get($name)
    {
        if ($name == 'top') {
            return $this->top();
        }

        if ($name == 'str') {
            return $this->el->str();
        }
    }
}