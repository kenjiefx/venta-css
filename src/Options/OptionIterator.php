<?php 

namespace Kenjiefx\VentaCSS\Options;

class OptionIterator implements \Iterator
{
    private int $position = 0;
    private array $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function current(): OptionModel
    {
        return $this->options[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return isset($this->options[$this->position]);
    }
}