<?php

namespace Kenjiefx\VentaCss\Build\CSS;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;

class CSSModel {

    private array $css;
    private string $source;
    private string $nativeChunk;
    private array $mediaQueryChunks;

    public function __construct()
    {
        $this->css = [];
        $nativeChunk = '';
        $mediaQueryChunks = '';
    }

    public function createToken (
        string $selectorName
        )
    {
        if (!isset($this->css[$selectorName])) {
            $this->css[$selectorName] = [];
        }
        return $this;
    }

    public function removeSelector (
        string $selectorName
        )
    {
        unset($this->css[$selectorName]);
        return $this;
    }

    public function setAttribute(
        string $selectorName,
        string $property,
        string $value
        )
    {
        $this->css[$selectorName][$property] = $value;
        return $this;
    }

    public function removeAttribute(
        string $className,
        string $property
        )
    {
        unset($this->css[$selectorName][$property]);
        return $this;
    }

    public function setSource(
        string $source
        )
    {
        $this->source = $source;
    }

    public function setNativeChunk(
        string $nativeChunk
        )
    {
        $this->nativeChunk = $nativeChunk;
    }

    public function getNativeChunk()
    {
        return $this->nativeChunk;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function sort()
    {
        asort($this->css);
    }

    public function exportTokens()
    {
        return $this->css;
    }

}
