<?php

namespace ColbyGatte\El;

class At
{
    use Sub;

    /**
     * @var int
     */
    protected $on;

    /**
     * @var array
     */
    protected $call;

    /**
     * Depth constructor.
     *
     * @param $on
     * @param \ColbyGatte\El\Element $el
     */
    public function __construct($on, $el)
    {
        $this->on = $on;
        $this->el = $el;
    }

    public function current()
    {
        return $this->on;
    }

    public function __call($name, $arguments)
    {
        $value = $this->on->$name(...$arguments);

        return $value === $this->on ? $this : $value;
    }
}