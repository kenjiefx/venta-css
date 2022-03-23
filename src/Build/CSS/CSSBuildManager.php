<?php

namespace Kenjiefx\VentaCss\Build\CSS;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Venta\Venta;
use \Kenjiefx\VentaCss\Build\CSS\DataLake;
use \Kenjiefx\VentaCss\Build\CSS\CSSModel;
use \Kenjiefx\VentaCss\Build\CSS\Utils;

class CSSBuildManager {

    private Venta $venta;
    private CSSModel $ParsedCSS;
    private CSSModel $RefinedCss;
    private string $css;

    public function __construct(
        Venta $venta
        )
    {
        $this->venta = $venta;
        $this->ParsedCSS = new CSSModel;
        $this->RefinedCss = new CSSModel;
    }

    public function build()
    {
        # First, we set the raw CSS file: venta/app.css
        $this->ParsedCSS->setRaw(
            rawCss: $this->venta->getCssToBuild()
        );

        # Next, we parse the raw CSS into an array
        Utils::parseRawCss($this->ParsedCSS);

        # Then, we sort the CSS array
        $this->ParsedCSS->sort();

        # Then, we register each of the CSS class

        echo json_encode($this->ParsedCSS->export());
    }







}
