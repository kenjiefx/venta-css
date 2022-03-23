<?php

namespace Kenjiefx\VentaCss\Build\CSS;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;

class CSSModel {

    private array $css;
    private string $rawCss;

    public function __construct()
    {
        $this->css = [];
    }

    public function addClass(
        string $className
        )
    {
        $this->css[$className] = [];
        return $this;
    }

    public function removeClass()
    {
        unset($this->css[$className]);
        return $this;
    }

    public function setAttribute(
        string $className,
        string $property,
        string $value
        )
    {
        $this->css[$className][$property] = $value;
        return $this;
    }

    public function removeAttribute(
        string $className,
        string $property
        )
    {
        unset($this->css[$className][$property]);
        return $this;
    }

    public function setRaw(
        string $rawCss
        )
    {
        $this->rawCss = $rawCss;
    }

    public function getRaw()
    {
        return $this->rawCss;
    }

    public function sort()
    {
        asort($this->css);
    }

    public function export()
    {
        return $this->css;
    }

}
