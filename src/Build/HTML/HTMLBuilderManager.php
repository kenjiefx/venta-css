<?php

namespace Kenjiefx\VentaCss\Build\HTML;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Venta\Venta;
use \Kenjiefx\VentaCss\Build\HTML\FileSys;


class HTMLBuilderManager {

    private Venta $venta;

    public function __construct(
        Venta $venta
        )
    {
        $this->venta = $venta;
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
                $references = json_decode(file_get_contents($closureArgs['backEnd'].'/venta/css.json'),TRUE);

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
                    }
                }

                $dom->save($filePath);

            }
        },['frontEnd'=>$this->venta->getFrontend(),'backEnd'=>$this->venta->getBackend()]);
    }

}
