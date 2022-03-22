<?php

namespace Kenjiefx\VentaCss\Build\CSS;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Venta\Venta;

class CSSBuilderFacade {

    private string $namespace;
    private Venta $venta;

    public function __construct(
        $namespace
        )
    {
        $this->namespace = $namespace;
        $this->venta     = new Venta($namespace);
    }



}
