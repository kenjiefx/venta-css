<?php

namespace Kenjiefx\VentaCss\Build\HTML;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Venta\Venta;

class HTMLBuilderFacade {

    private string $namespace;
    private Venta $venta;

    public function __construct(
        string $namespace,
        Venta $venta
        )
    {
        $this->namespace = $namespace;
        $this->venta = $venta;
    }



}
