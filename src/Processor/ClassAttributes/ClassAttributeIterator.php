<?php 

namespace Kenjiefx\VentaCSS\Processor\ClassAttributes;

class ClassAttributeIterator implements \Iterator {

    private int $position = 0;
    private array $classAttributes;

    public function __construct(array $classAttributes) {
        $this->classAttributes = $classAttributes;
    }

    public function current(): ClassAttributeModel {
        return $this->classAttributes[$this->position];
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
        return isset($this->classAttributes[$this->position]);
    }

}