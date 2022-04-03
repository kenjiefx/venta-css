<?php

namespace Kenjiefx\VentaCss\Build;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Venta\Venta;
use \Kenjiefx\VentaCss\Build\HTML\FileSys;
use \Kenjiefx\VentaCss\Build\BuilderFacadeInterface;
use \Kenjiefx\VentaCss\Build\CSS\CSSBuilderFacade;
use \Kenjiefx\VentaCss\Build\HTML\HTMLBuilderFacade;
use \Kenjiefx\VentaCss\Build\Compiler\Compiler;

class BuildManager implements BuilderFacadeInterface {

    private string|null $namespace;
    private CSSBuilderFacade $CSSBuilderFacade;
    private HTMLBuilderFacade $HTMLBuilderFacade;
    private Compiler $Compiler;
    private int $startTime;

    public function __construct(
        array $argv
        )
    {
        $this->loadTools();
    }

    public function loadTools()
    {
        $this->startVenta()
             ->loadBuilder(CSSBuilderFacade::class)
             ->loadBuilder(HTMLBuilderFacade::class);
    }

    private function startVenta()
    {
        $this->venta = new Venta();
        return $this;
    }

    private function loadBuilder(
        string $Builder
        )
    {
        $builder = new $Builder($this->venta);
        $name    = (new \ReflectionClass($builder))->getShortName();
        $this->$name = $builder;
        return $this;
    }

    public function build()
    {

        $this->CSSBuilderFacade->build();
        exit();

        $this->HTMLBuilder->build();
        $this->Compiler->compile();

        CoutStreamer::cout('Successfully compressed files!','success');

        $newFileSize = FileSys::getSize($this->venta->getFrontend().'/venta/app.css');
        CoutStreamer::cout('Total build time: '.(microtime(true)-$timeStart).' seconds');

    }



}
