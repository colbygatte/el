<?php

namespace ColbyGatte\El;

class Slice
{
    use Sub;

    /**
     * @var \ColbyGatte\El\Element
     */
    protected $element;

    /**
     * Depth constructor.
     *
     * @param $element
     * @param \ColbyGatte\El\Element $el
     */
    public function __construct($element, $el)
    {
        $this->element = $element;
        $this->el = $el;
    }

    public function current()
    {
        return $this->element;
    }

    public function __call($name, $arguments)
    {
        $this->element->$name(...$arguments);

        return $this;
    }
}