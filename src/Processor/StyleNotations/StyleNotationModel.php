<?php 

namespace Kenjiefx\VentaCSS\Processor\StyleNotations;

use Kenjiefx\VentaCSS\Breakpoints\BreakpointModel;
use Kenjiefx\VentaCSS\Processor\PseudoClasses\PseudoClassEnum;

class StyleNotationModel {

    public function __construct(
        /**
         * The individual style notation in the class attribute. 
         * For example, "display:flex" or "text:24:hover" or "display:none@mobile".
         */
        public readonly string $value,

        /**
         * The minified name of the style notation.
         * For example, "ox7" for "display:flex" or "tyd" for "width:23:hover".
         */
        public readonly string $minifiedName,

        /**
         * The pseudo-class associated with this style declaration, if any.
         */
        public readonly PseudoClassEnum | null $pseudoClass,

        /**
         * The breakpoint model associated with this style declaration, if any.
         */
        public readonly BreakpointModel | null $breakpoint,

        /**
         * Array of CSS declarations that this style notation represents.
         * Example of CSS declaration is, ".ox7{display:flex;}" or ".dark.vsy{color:white;}".
         */
        public readonly array $cssRegisters

    ) {}

}