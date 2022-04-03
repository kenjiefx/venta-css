<?php

namespace Kenjiefx\VentaCss\Build\CSS;
use \Kenjiefx\VentaCss\Cli\Console;
use \Kenjiefx\VentaCss\Venta\Venta;
use \Kenjiefx\VentaCss\Build\BuilderFacadeInterface;
use \Kenjiefx\VentaCss\Build\CSS\CSSBuildManager;

class CSSBuilderFacade implements BuilderFacadeInterface{

    private string $namespace;
    private Venta $venta;
    private CSSBuildManager $cssBuildManager;

    public function __construct(
        Venta $venta
        )
    {
        $this->venta = $venta;
        $this->namespace = $this->venta->namespace;
        $this->cssBuildManager = new CSSBuildManager($venta);
    }

    public function build()
    {
        Console::out('Compressing venta/app.css');
        $this->cssBuildManager->build();
    }

}
