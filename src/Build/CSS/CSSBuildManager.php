<?php

namespace Kenjiefx\VentaCss\Build\CSS;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Venta\Venta;
use \Kenjiefx\VentaCss\Build\CSS\DataLake;

class CSSBuildManager {

    private Venta $venta;
    private DataLake $DataLake;
    private string $css;

    public function __construct(
        Venta $venta
        )
    {
        $this->venta = $venta;
        $this->DataLake = new DataLake;
    }

    public function build()
    {
        $this->css = $this->venta->getCssToBuild();
        $this->cssToArray();
    }

    public function cssToArray()
    {
        preg_match_all( '/(?ims)([a-z0-9*\s\,\.\:#_\-@]+)\{([^\}]*)\}/',$this->css,$arr);
        foreach ($arr[0] as $i => $x) {
            # Registering a new css selector
            $selector = trim($arr[1][$i]);
            $this->DataLake->feed($selector,[]);
            $rules = explode(';', trim($arr[2][$i]));

            foreach ($rules as $strRule) {
                if (!empty($strRule)){
                    $rule = explode(":", $strRule);
                    $this->DataLake->inject($selector,[
                        trim($rule[0]),
                        trim($rule[1])
                    ]);
                }
            }
        }
        $this->DataLake->sort();
    }

}
