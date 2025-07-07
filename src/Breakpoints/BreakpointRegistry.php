<?php 

namespace Kenjiefx\VentaCSS\Breakpoints;

class BreakpointRegistry {

    private static array $breakpoints = [];

    public function __construct(

    ) {}

    public function register(
        string $name,
        BreakpointModel $breakpoint
    ): void {
        // Override existing breakpoint if it exists
        self::$breakpoints[$name] = $breakpoint;
    }

    public function getByName(
        string $name
    ): ?BreakpointModel {
        return self::$breakpoints[$name] ?? null;
    }

}