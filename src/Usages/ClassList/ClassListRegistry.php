<?php 

namespace Kenjiefx\VentaCSS\Usages\ClassList;

class ClassListRegistry
{
    
    private array $usedClassList = [];

    public function register(string $classList): void
    {
        array_push($this->usedClassList, $classList);
    }

    public function getAll(): array
    {
        return $this->usedClassList;
    }

    public function clear(): void
    {
        $this->usedClassList = [];
    }
}