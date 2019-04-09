<?php

namespace ColbyGatte\El;

class OpenTag extends Tag
{
    protected $attributes;

    /**
     * @var bool
     */
    protected $selfClosing;

    public function __construct($tag, $attributes = [])
    {
        if ($tag[-1] == '/') {
            $this->selfClosing = true;

            $tag = trim($tag, '/');
        }

        $data = preg_split('/(#|\.)/', $tag, -1, PREG_SPLIT_DELIM_CAPTURE);
        $tag = array_shift($data);

        parent::__construct($tag);

        $this->attributes = ['class' => []];

        $this->parseAttributes($data, $attributes);
    }

    public static function make($item, $tag)
    {
        return [
            $open = new static($tag),
            $item,
            $open->closing()
        ];
    }

    public function parseAttributes($selector, $attributes = [])
    {
        $data = is_string($selector) ? array_filter(
            preg_split('/(#|\.)/', $selector, -1, PREG_SPLIT_DELIM_CAPTURE)
        ) : $selector;

        $class = $attributes['class'] ?? '';
        unset($attributes['class']);

        $this->attributes = array_merge($this->attributes, $attributes);
        $this->attributes['class'] = array_merge($this->attributes['class'],
            is_string($class)
                ? array_map('trim', explode(' ', $class))
                : $class
        );

        if ($data) {
            foreach (array_chunk($data, 2, false) as $item) {
                ($item[0] == '#')
                    ? $this->attributes['id'] = $item[1]
                    : array_push($this->attributes['class'], $item[1]);
            }
        }

        return $attributes;
    }

    public function closing()
    {
        return new Tag($this->selfClosing ? null : $this->tag);
    }

    public function buildAttributes()
    {
        $return = [];

        foreach ($this->attributes as $key => $value) {
            if ($key == 'class') {
                if (empty($value = array_filter($value))) {
                    continue;
                }

                $value = implode(' ', $value);
            }

            $return[] = sprintf('%s="%s"', htmlentities($key), htmlentities($value));
        }

        return implode(' ', $return);
    }

    public function __toString()
    {
        $attributes = $this->buildAttributes();

        return sprintf('<%s%s%s>', $this->tag, $attributes ? ' '.$attributes : '', $this->selfClosing ? '/' : '');
    }
}
