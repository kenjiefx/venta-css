<?php

namespace Kenjiefx\VentaCss\Config;
use \Kenjiefx\VentaCss\VentaFacadeTraits;
use \Kenjiefx\VentaCss\Config\ConfigManager;

class ConfigFacade {

    use VentaFacadeTraits;

    /**
     * @var array
     * Command line inputs
     */
    private array $argv;

    /**
     * @var ReflectionClass
     */
    private \ReflectionClass $VentaManager;

    public function __construct(
        array $argv
        )
    {
        $this->argv = $argv;
        $this->VentaManager = new \ReflectionClass(ConfigManager::class);
    }

    public function __call($fn,$args)
    {
        if (!$this->hasMethod($fn)) return;
        $this->invokeMethod($fn,$this->argv);
    }


}
