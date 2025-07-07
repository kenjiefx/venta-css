<?php 

namespace Kenjiefx\VentaCSS\Classes;

class ClassIterator implements \Iterator {

    private array $classes;
    private int $position = 0;

    public function __construct(array $classes) {
        $this->classes = $classes;
    }

    public function current(): ClassModel {
        return $this->classes[$this->position];
    }

    public function key(): int {
        return $this->position;
    }

    public function next(): void {
        ++$this->position;
    }

    public function rewind(): void {
        $this->position = 0;
    }

    public function valid(): bool {
        return isset($this->classes[$this->position]);
    }

}