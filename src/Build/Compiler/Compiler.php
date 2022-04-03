<?php

namespace Kenjiefx\VentaCss\Build\Compiler;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Venta\Venta;
use \Kenjiefx\VentaCss\Build\CSS\CSSModel;
use \Kenjiefx\VentaCss\Build\CSS\CSSChunker;
use \Kenjiefx\VentaCss\Build\CSS\Utils;

class Compiler {

    private string|null $namespace;
    private Venta $venta;

    public function __construct(
        string|null $namespace,
        Venta $venta
        )
    {
        $this->venta = $venta;
        $this->namespace = $this->venta->namespace;
    }

    public function compile()
    {
        $css = '';
        $lookup = $this->getLookUp();
        $usables = $this->getUsables();
        $map = $this->getMapper();

        foreach ($usables as $htmlRef) {
            if (isset($lookup[$htmlRef])) {
                foreach ($lookup[$htmlRef]['css'] as $selector => $rules) {
                    $css = $css.'.'.$selector.' {';
                    foreach ($rules as $prop => $val) {
                        $css = $css.$prop.':'.$val.';';
                    }
                    $css = $css.'} ';
                }
            }
        }

        $chunker     = new CSSChunker($this->venta->getCssToBuild());
        $mediaBlocks = $chunker->init()->getMediaBlocks();
        foreach ($mediaBlocks as $mediaBlock) {
            $statement = CSSChunker::getMediaBlockStatement($mediaBlock);
            $css = $css.' '.trim($statement).'{';
            $contents = CSSChunker::getMediaBlockContent($mediaBlock);
            $MediaCSS = new CSSModel;
            $MediaCSS->setRaw($contents);
            Utils::parseRawCss($MediaCSS);
            foreach ($MediaCSS->export() as $selector => $rules) {
                $minifiedNames = explode(' ',$map[$selector]);
                foreach ($minifiedNames as $minifiedName) {
                    if (in_array($minifiedName,$usables)) {
                        $css = $css.'.'.implode('.',$minifiedNames).' {';
                        foreach ($rules as $prop => $val) {
                            $css = $css.$prop.':'.$val.';';
                        }
                        $css = $css.'} ';
                        break;
                    }
                }
            }
            $css = $css.' }';
        }

        file_put_contents($this->venta->getFrontend().'/venta/app.css',$css);
    }

    private function getLookUp()
    {
        return json_decode(file_get_contents($this->venta->getBackend().'/venta/__venta.css.json'),TRUE);
    }

    private function getUsables()
    {
        return json_decode(file_get_contents($this->venta->getBackend().'/venta/__venta.usables.json'),TRUE);
    }

    private function getMapper()
    {
        return json_decode(file_get_contents($this->venta->getBackend().'/venta/__venta.map.json'),TRUE);
    }





}
