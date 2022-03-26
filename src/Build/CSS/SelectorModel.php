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
    public string|null $pseudoSeparator;

    public function __construct(
        string $realName
        )
    {
        $this->realName = $realName;
        $this->hasPseudo = false;
        $this->pseudoClass = null;
        $this->pseudoSeparator = null;
        $this->parentOf = null;
        $this->childOf = null;
        $this->typeOf = 'element';
        $this->parse();
    }

    public function minifyName(
        array $registrar
        )
    {
        if ($this->realName==='*') {
            $this->minifiedName = '*';
            return;
        }

        if (!str_contains($this->realName,'.')) {
            if (!str_contains($this->realName,'#')) {
                $this->minifiedName = $this->realName;
                return;
            }
        }



        $this->minifiedName = Utils::createClassName($registrar);

        return;

    }

    private function parse()
    {
        if (str_contains($this->realName,':')) {
            $this->hasPseudo = true;
            $this->pseudoSeparator = ':';
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
