<?php 

namespace Kenjiefx\VentaCSS\Classes;

class ClassModel {

    public function __construct(
        public readonly ClassKey $key,
        public readonly string $declaration
    ) {}

}