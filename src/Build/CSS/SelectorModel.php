<?php

namespace Kenjiefx\VentaCss\Build\CSS;
use \Kenjiefx\VentaCss\Build\CSS\Utils;

class SelectorModel {

    public string $name;
    public string $minifiedName;
    public string $typeOf;
    public bool $hasPseudo;
    public string $pseudo;

    public function __construct(
        string $name
        )
    {
        $this->name = $name;
        $this->typeOf = 'selector';
        $this->hasPseudo = false;
        $this->pseudo = '';
        $this->parse();
    }

    public function minifyName(
        array $existingNames = null
        )
    {
        $minifiedName = Utils::createClassName($existingNames??[]);

        /**
         * When the selector is *, we need to
         * force the classname to be just *
         */
        if ($this->name==='*') {
            $this->minifiedName = '*';
            return;
        }

        if ($this->typeOf==':pseudo') {
            $this->minifiedName = $minifiedName.':'.$this->pseudo;
            return;
        }

        $this->minifiedName = $minifiedName;
        return;

    }

    private function parse()
    {
        if ($this->name==='*') {
            $this->typeOf = '*';
            return;
        }

        if (str_contains($this->name,':')) {
            $this->typeOf    = ':pseudo';
            $this->hasPseudo = true;
            $this->pseudo    = explode(':',$this->name)[1];
            return;
        }
    }



}
