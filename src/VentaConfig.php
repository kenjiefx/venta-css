<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS;

class VentaConfig {

    private static string $CONFIG_FILE = __dir__.'/Assets/venta.config.json';
    private array $raw_config_options = [];

    public function __construct()
    {
        $this->unpack_config_values();
    }

    private function unpack_config_values()
    {
        $this->raw_config_options = json_decode(
            file_get_contents(VentaConfig::$CONFIG_FILE),TRUE
        );
    }

    public function get_attribute(
        string $attribute_name
        )
    {
        return $this->raw_config_options[$attribute_name] ?? [];
    }

    public function get_attribute_list(){
        $attribute_list = [];
        foreach ($this->raw_config_options as $attribute_name => $attribute_data) {
            array_push($attribute_list,$attribute_name);
        }
        return $attribute_list;
    }

}
