<?php

namespace Kenjiefx\VentaCss\Build\CSS;
use \Kenjiefx\VentaCss\Build\CSS\Utils;

class SelectorModel {

    public string $realName;
    public string $minifiedName;
    public string $typeOf;
    public string|null $parentOf;
    public string|null $childOf;

    # Pseudo Class
    public bool $hasPseudo;
    public string|null $pseudoClass;

    public function __construct(
        string $realName
        )
    {
        $this->realName = $realName;
        $this->hasPseudo = false;
        $this->pseudoClass = null;
        $this->typeOf = 'element';
        $this->parse();
    }

    public function minifyName(
        array $registrar
        )
    {
        if ($this->realName==='*') {
            $this->minifiedName = '*';
        }

        $this->minifiedName = Utils::createClassName($registrar);

        return;

    }

    private function parse()
    {
        if (str_contains($this->realName,':')) {
            $this->hasPseudo = true;
            $this->pseudoClass = explode(':',$this->realName)[1];
        }
        if (str_contains($this->realName,'.')) {
            $this->typeOf = 'class';
        }
        if (str_contains($this->realName,'#')) {
            $this->typeOf = 'id';
        }

    }

    private function findParents(
        array $existingReferences,
        string $parentName
        )
    {

    }



}
