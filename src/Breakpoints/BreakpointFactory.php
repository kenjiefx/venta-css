<?php 

namespace Kenjiefx\VentaCSS\Breakpoints;

use Kenjiefx\VentaCSS\Tokens\MinifiedTokenPool;

class BreakpointFactory {

    public function __construct(
        
    ) {}

    /**
     * Creates a BreakpointModel instance from the given parameters.
     *
     * @param string $name The name of the breakpoint.
     * @return BreakpointModel
     */
    public function create(
        string $name,
        string $mediaQuery
    ): BreakpointModel {
        return new BreakpointModel(
            name: $name,
            mediaQuery: $mediaQuery
        );
    }

}