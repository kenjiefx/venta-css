<?php

namespace Kenjiefx\VentaCss;

#[\Attribute]
class ManagerRegistry {

    private string $manager;

    public function __construct(
        $manager
        )
    {
        $this->manager = $manager;
    }

    public function dumpSequence()
    {
        $manager = $this->manager;
        return $this->$manager();
    }

    public function ConfigManager()
    {
        return ['ready','verify'];
    }

}
