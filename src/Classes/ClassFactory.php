<?php 

namespace Kenjiefx\VentaCSS\Classes;

class ClassFactory {

    public function __construct(
        
    ) {}

    public function create(
        string $theme,
        string $property,
        string $declaration,
        string $variant
    ) {
        $classKey = new ClassKey(
            property: $property,
            variant: $variant,
            theme: $theme
        );
        return new ClassModel(
            key: $classKey,
            declaration: $declaration
        );
    }


}