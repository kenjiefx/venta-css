<?php

namespace Kenjiefx\VentaCss;

trait VentaFacadeTraits {

    protected function hasMethod(
        string $fnName
        )
    {
        return $this->VentaManager->hasMethod($fnName);
    }

    protected function invokeMethod(
        string $fnName,
        array $argv
        )
    {

        $manager   = $this->loadManager($argv);
        $sequences = $this->loadSequence();

        $use = false;

        foreach ($sequences as $sequence) {
            if ($sequence===$fnName) $use = true;
            if ($use) {
                $this->logMethod(
                    new \ReflectionMethod($manager,$sequence)
                );
            }
        }
    }

    private function loadManager(
        array $argv
        )
    {
        return $this->VentaManager->newInstance($argv);
    }

    private function loadSequence()
    {
        foreach ($this->VentaManager->getAttributes() as $attribute) {
            return $attribute->newInstance()->dumpSequence();
        }
    }

    private function logMethod(
        \ReflectionMethod $ManagerMethod
        )
    {
        foreach ($ManagerMethod->getAttributes() as $attribute) {
            $attribute->newInstance();
        }
    }




}
