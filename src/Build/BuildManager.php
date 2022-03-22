<?php

namespace Kenjiefx\VentaCss\Build;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Venta\Venta;
namespace Kenjiefx\VentaCss\Build\CSS\CSSBuilderFacade;

class BuildManager {

    private string $namespace;

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
            CoutSteamer::cout("Error {$e->getMessage()}",'error');
            exit();
        }

        $this->namespace = $argv[2];
        $this->loadNamespace();
    }

    /**
     * @throws Exception
     * When namespace isn't found
     */
    public function loadNamespace()
    {
        $this->venta = new Venta($this->namespace);
    }

    public function buildCss()
    {
        
    }



}
