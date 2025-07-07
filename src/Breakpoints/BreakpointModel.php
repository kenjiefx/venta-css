<?php 

namespace Kenjiefx\VentaCSS\Breakpoints;

class BreakpointModel {

    public function __construct(
        public readonly string $name,
        public readonly string $mediaQuery
    ) {}

}