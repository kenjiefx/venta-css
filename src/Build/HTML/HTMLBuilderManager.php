<?php

namespace Kenjiefx\VentaCss\Build\HTML;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Venta\Venta;
use \Kenjiefx\VentaCss\Build\HTML\FileSys;


class HTMLBuilderManager {

    private Venta $venta;
    private array $usables;
    private array $compiled;

    public function __construct(
        Venta $venta
        )
    {
        $this->venta = $venta;
        $this->usables = [];
        $this->compiled = [];
        //$this->getCompiledCss();
    }

    public function build()
    {
        FileSys::traverse($this->venta->getFrontend(),function(
            $filePath,
            $fileName,
            $fileExtension,
            $closureArgs
        ){
            if ($fileExtension==='html'||$fileExtension==='htm'||$fileExtension==='php') {
                require_once __dir__.'/simple_html_dom.php';
                $dom = file_get_html($filePath);
                $references = json_decode(file_get_contents($closureArgs['backEnd'].'/venta/__venta.css.json'),TRUE);

                foreach ($references as $realName => $minifiedName) {
                    if ($realName==='*') continue;
                    if (str_contains($realName,':')) {
                        $realName = explode(':',$realName)[0];
                    }
                    if (str_contains($realName,'::')) {
                        $realName = explode('::',$realName)[0];
                    }
                    if ($realName[0]!=='.'&&$realName[0]!=='#') {
                        continue;
                    }
                    foreach($dom->find($realName) as $element) {
                        if ($realName[0]==='.') {
                            $realName = substr($realName,1);
                        }
                        if ($realName[0]==='#') {
                            $realName = substr($realName,1);
                        }
                        $element->removeClass($realName);
                        $element->addClass($minifiedName);
                        $element->removeClass('1');
                        $this->addToUsables($minifiedName);
                    }
                }

                $dom->save($filePath);

            }
        },['frontEnd'=>$this->venta->getFrontend(),'backEnd'=>$this->venta->getBackend()]);

        $this->createAppCss();


    }

    private function getCompiledCss()
    {
        $this->compiled = json_decode(file_get_contents($this->venta->getBackend().'/venta/__venta.map.json'),TRUE);
    }

    private function addToUsables(
        string $minifiedName
        )
    {
        /**
         * @TODO Create a way to only export css that has been used throughout the project
         */

    }

    private function createAppCss()
    {

    }

}
