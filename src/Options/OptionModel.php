<?php 

namespace Kenjiefx\VentaCSS\Options;

class OptionModel {

    public function __construct(
        public readonly OptionType $type,
        public readonly string $theme,
        public readonly string $property,
        public readonly string $rule,
        public readonly array $values
    ) {}

}