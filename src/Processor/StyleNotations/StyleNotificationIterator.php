<?php 

namespace Kenjiefx\VentaCSS\Processor\StyleNotations;

class StyleNotificationIterator implements \Iterator
{
    private int $position = 0;
    private array $styleNotations;

    public function __construct(array $styleNotations)
    {
        $this->styleNotations = $styleNotations;
    }

    public function current(): StyleNotationModel
    {
        return $this->styleNotations[$this->position];
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
        return isset($this->styleNotations[$this->position]);
    }
}