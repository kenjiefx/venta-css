<?php

namespace Kenjiefx\VentaCss\Build;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Venta\Venta;
use \Kenjiefx\VentaCss\Build\BuilderFacadeInterface;
use \Kenjiefx\VentaCss\Build\CSS\CSSBuilderFacade;
use \Kenjiefx\VentaCss\Build\HTML\HTMLBuilderFacade;

class BuildManager implements BuilderFacadeInterface {

    private string $namespace;
    private CSSBuilderFacade $CSSBuilder;
    private int $startTime;

    public function __construct(
        array $argv
        )
    {
        try {
            if (!isset($argv[2])) {
                throw new \Exception(
                    'Build command requires directory'
                );
            }
        } catch (\Exception $e) {
            CoutStreamer::cout("Error {$e->getMessage()}",'error');
            exit();
        }
        $this->startTime = microtime(true);
        $this->namespace = $argv[2];
        $this->loadTools();
    }

    /**
     * @throws Exception
     * When namespace isn't found
     */
    public function loadTools()
    {
        $this->venta = new Venta($this->namespace);
        $this->CSSBuilder = new CSSBuilderFacade(
            $this->namespace,
            $this->venta
        );
        $this->HTMLBuilder = new HTMLBuilderFacade(
            $this->namespace,
            $this->venta
        );
    }


    public function build()
    {
        $this->CSSBuilder->build();
        $this->HTMLBuilder->build();
    }



}
