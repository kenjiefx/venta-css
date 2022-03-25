<?php

namespace Kenjiefx\VentaCss\Build\CSS;
use \Kenjiefx\VentaCss\Build\CSS\Utils;

class SelectorModel {

    public string $name;
    public string $minifiedName;
    public string $typeOf;
    public bool $hasPseudo;
    public bool $hasChildren;
    public string $pseudo;
    public string $parent;
    public array $children;

    public function __construct(
        string $name
        )
    {
        $this->name = $name;
        $this->typeOf = 'selector';
        $this->hasPseudo = false;
        $this->hasChildren = false;
        $this->parent = '';
        $this->children = [];
        $this->pseudo = '';
        $this->parse();
    }

    public function minifyName(
        array $existingNames = null,
        array $existingReferences = null
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

        if ($this->typeOf=='.parent .sibling') {
            $minifiedParentName = $this->findParents($existingReferences??[],$this->parent);
            if (null===$minifiedParentName) $minifiedParentName = Utils::createClassName($existingNames??[]);
            $this->minifiedName = $minifiedParentName.' '.Utils::createClassName($existingNames??[]);
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

        if (str_contains($this->name,' ')) {
            $this->typeOf      = '.parent .sibling';
            $this->hasChildren = true;
            $this->parent = explode(' ',$this->name)[0];
            array_push($this->children,explode(' ',$this->name)[1]);
        }
    }

    private function findParents(
        array $existingReferences,
        string $parentName
        )
    {
        foreach ($existingReferences as $family => $minifiedReference) {
            $thisFamily = explode(' ',$family);
            if ($thisFamily[0]===$parentName) {
                return explode(' ',$minifiedReference)[0];
            }
            if ('.'.$thisFamily[0]===$parentName) {
                return explode(' ',$minifiedReference)[0];
            }
            if ('#'.$thisFamily[0]===$parentName) {
                return explode(' ',$minifiedReference)[0];
            }
        }
        return null;
    }



}
