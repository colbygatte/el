<?php

namespace ColbyGatte\El;

class Depth
{
    use Sub;

    /**
     * @var int
     */
    protected $diveToDepth;

    /**
     * @var array
     */
    protected $call;

    /**
     * Depth constructor.
     *
     * @param int $depth
     * @param \ColbyGatte\El\Element $el
     */
    public function __construct($depth, $el)
    {
        $this->diveToDepth = $depth;
        $this->el = $el;
    }

    /**
     * @param int $depth
     * @param array|mixed $content
     * @return array|\ColbyGatte\El\Element|mixed
     */
    protected function dive($content, $depth = 0)
    {
        if ($depth != $this->diveToDepth) {
            return is_array($content) ? array_map(function ($item) use ($depth) {
                return $this->dive($item, $depth + 1);
            }, $content) : $content;
        }

        call_user_func($this->call, $content = Element::wrap($content));

        return $content;
    }

    public function __call($name, $arguments)
    {
        $this->call = function ($content) use ($name, $arguments) {
            call_user_func_array([$content, $name], $arguments);
        };

        $this->el->pipe(function ($content) {
            return $this->dive($content);
        });

        return $this;
    }
}