<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS;

class VentaConfig {

    private static string $CONFIG_FILE = __dir__.'/Assets/venta.config.json';
    private array $rawConfigOptions = [];

    public function __construct()
    {
        $this->distrubuteConfigValues();
    }

    private function distrubuteConfigValues()
    {
        $this->rawConfigOptions = json_decode(
            file_get_contents(VentaConfig::$CONFIG_FILE),TRUE
        );

    }

    public function getSettings(
        string $settingName
        )
    {
        return $this->rawConfigOptions[$settingName] ?? [];
    }

}
