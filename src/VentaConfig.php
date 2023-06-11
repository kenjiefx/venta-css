<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS;
use Kenjiefx\ScratchPHP\App\Themes\ThemeController;

class VentaConfig {

    private static string $CONFIG_FILE = __dir__.'/Assets/venta.config.json';

    private static string $CUSTOM_CONFIG_FILE = '/venta/venta.config.json';
    private static array $raw_config_options = [];

    private ThemeController $ThemeController;

    public function __construct()
    {
        
    }

    public function unpack_config_values()
    {
        if (empty(static::$raw_config_options)) {

            $this->ThemeController = new ThemeController;
            static::$raw_config_options = json_decode(
                file_get_contents(VentaConfig::$CONFIG_FILE),TRUE
            );

            /**
             * At this point, we are retrieving custom Venta CSS config in
             * the active theme directory.
             */
            $custom_venta_config_path = $this->ThemeController->getThemePath().static::$CUSTOM_CONFIG_FILE;
            if (file_exists($custom_venta_config_path)) {

                $sanitized_custom_config_options = $this->sanitize_config_option(
                    json_decode(file_get_contents($custom_venta_config_path),TRUE)
                );

                foreach ($sanitized_custom_config_options as $config_name => $configuration) {
                    static::$raw_config_options[$config_name] = $configuration;
                }
            }

        }
    }

    public function get_attribute(string $attribute_name)
    {
        return static::$raw_config_options[$attribute_name] ?? [];
    }

    public function get_attribute_list(){
        $attribute_list = [];
        foreach (static::$raw_config_options as $attribute_name => $attribute_data) {
            array_push($attribute_list,$attribute_name);
        }
        return $attribute_list;
    }

    /**
     * @TODO Sanitization logic here for custom config options
     */
    private function sanitize_config_option(array $custom_config_options){
        return $custom_config_options;
    }

}
