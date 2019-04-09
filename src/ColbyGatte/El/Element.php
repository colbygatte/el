<?php

namespace ColbyGatte\El;

use Exception;
use Throwable;

class Element
{
    protected $content;

    /**
     * @var \ColbyGatte\El\OpenTag
     */
    protected $tag;

    /**
     * @var bool
     */
    protected $safeMode = false;

    protected $topElement;

    public function __construct($content = [], $topElement = null)
    {
        $this->content = is_array($content) ? $content : [$content];
        $this->topElement = $topElement;
    }

    public static function wrap($content)
    {
        return $content instanceof Element
            ? $content
            : new static($content);
    }

    public function tag($tag)
    {
        array_unshift($this->content, $this->tag = new OpenTag($tag));
        array_push($this->content, $this->tag->closing());

        return $this;
    }

    public function pipe(callable $callback)
    {
        $this->content = $callback($this->content);

        return $this;
    }

    public function add(...$items)
    {
        $key = array_key_last($this->content);

        if ($key && $this->content[$key] instanceof Tag) {
            array_splice($this->content, count($this->content) - 1, 0, $items);
        } else {
            $this->content[] = $items;
        }

        return $this;
    }

    public function after(...$items)
    {
        $this->content[] = $items;

        return $this;
    }

    public function each($tag)
    {
        $this->content = array_map(function ($item) use ($tag) {
            return OpenTag::make($item, $tag);;
        }, $this->content);

        return $this;
    }

    public function tagBefore($offset, $tag)
    {
        // Splice it, map it, unshift it
        array_unshift($this->content, ...array_map(function ($item) use ($tag) {
            return OpenTag::make($item, $tag);
        }, array_splice($this->content, 0, $offset)));

        return $this;
    }

    public function tagFrom($offset, $tag)
    {
        // Splice it, map it, push it
        array_push($this->content, ...array_map(function ($item) use ($tag) {
            return OpenTag::make($item, $tag);
        }, array_splice($this->content, $offset)));

        return $this;
    }

    public function getTop()
    {
        return $this->topElement ?: $this;
    }

    public function setTop($element)
    {
        $this->topElement = $element;

        return $this;
    }

    public function depth($depth)
    {
        return new Depth($depth, $this);
    }

    public function slice($offset, $length = null)
    {
        $slice = $this->transformedSlice($offset, $length);

        return new At(new Element($slice, $this), $this);
    }

    /**
     * @param int $offset
     * @param int $length
     * @param callable $callback
     * @return array
     */
    public function transformedSlice($offset, $length, callable $callback = null)
    {
        $callback = $callback ?: [static::class, 'wrap'];

        $slice = is_null($length)
            ? array_splice($this->content, $offset)
            : array_splice($this->content, $offset, $length);

        $slice = array_map($callback, $slice);

        array_splice($this->content, $offset, 0, $slice);

        return $slice;
    }

    public function at($index)
    {
        return new At(
            $this->content[$index] = static::wrap($this->content[$index]), $this
        );
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    public function safe()
    {
        $this->safeMode = true;

        return $this;
    }

    public function unsafe()
    {
        $this->safeMode = false;

        return $this;
    }

    public function str($item = null)
    {
        if (is_null($item)) {
            return $this->__toString();
        } elseif (is_array($item)) {
            return implode('', array_map([$this, 'str'], $item));
        } else {
            return $this->safeMode && ! $item instanceof Tag
                ? htmlentities((string) $item)
                : (string) $item;
        }
    }

    public function __toString()
    {
        try {
            return $this->str($this->content);
        } catch (Throwable | Exception $e) {
            return '{error}';
        }
    }

    public function __get($name)
    {
        if (substr($name, 0, 3) == 'tag') {
            $this->tag(lcfirst(substr($name, 3)));

            return new Attribute($this->tag, $this);
        }

        switch ($name) {
        case 'attr':
            return new Attribute($this->tag, $this);
        case 'str':
            return $this->str();
        }
    }
}