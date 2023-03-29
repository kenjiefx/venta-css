<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS\Services;

class VentaDashboard {

    private array $selectorEntities = [];

    public function addEntity(
        $groupName,
        $selectorType,
        $selectorTypeDescription,
        $selectorName,
        $selectorValue
        )
    {
        if (!isset($this->selectorEntities[$groupName])) {
            $this->selectorEntities[$groupName] = [];
        }
        if (!isset($this->selectorEntities[$groupName][$selectorType])) {
            $this->selectorEntities[$groupName][$selectorType]['description'] = $selectorTypeDescription;
            $this->selectorEntities[$groupName][$selectorType]['variants'] = [];
        }
        $this->selectorEntities[$groupName][$selectorType]['variants'][$selectorName] = $selectorValue;
    }


    public function export()
    {
        return $this->selectorEntities;
    }

}
