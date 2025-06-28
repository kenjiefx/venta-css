<?php 

namespace Kenjiefx\VentaCSS\Classes;

class ClassKey {

    public function __construct(
        public readonly string $property,
        public readonly string $variant,
        public readonly string $theme
    ) {}

    public function __toString(): string {
        return "{$this->property}:{$this->variant}({$this->theme})";
    }

}