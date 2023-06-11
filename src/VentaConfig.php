<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS;
use Kenjiefx\ScratchPHP\App\Themes\ThemeController;

class VentaConfig {

    private static string $CONFIG_FILE = __dir__.'/Assets/venta.config.json';
    private static string $CUSTOM_CONFIG_FILE = '/venta/venta.config.json';
    private static array $raw_config_options = [];
    private static array $media_breakpoints = [];

    private ThemeController $ThemeController;

    public function __construct()
    {
        
    }

    public function unpack_config_values()
    {
        if (empty(static::$raw_config_options)) {

            # Instantiating the Theme Controller object from ScratchPHP
            $this->ThemeController = new ThemeController;

            # Instantiating config from the built-in config file
            $config_options = json_decode(file_get_contents(VentaConfig::$CONFIG_FILE),TRUE);

            # Unpacking non-attribute configuration. For example, "media-breakpoints"
            $config_options = $this->unpack_media_breakpoints($config_options);

            # Retrieving custom Venta CSS config in the active theme directory, if there is any
            $custom_venta_config_path = $this->ThemeController->getThemePath().static::$CUSTOM_CONFIG_FILE;
            if (file_exists($custom_venta_config_path)) {

                $sanitized_custom_config_options = $this->sanitize_config_option(
                    json_decode(file_get_contents($custom_venta_config_path),TRUE)
                );

                foreach ($sanitized_custom_config_options as $config_name => $configuration) {
                    $config_options[$config_name] = $configuration;
                }
            }

            static::$raw_config_options = $config_options;

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

    public function get_media_breakpoint(string $alias){
        return static::$media_breakpoints[$alias] ?? [];
    }

    public function get_media_breakpoint_aliases() {
        $media_breakpoint_aliases = [];
        foreach (static::$media_breakpoints as $alias => $media_breakpoint_config) {
            array_push($media_breakpoint_aliases, $alias);
        }
        return $media_breakpoint_aliases;
    }

    /**
     * @TODO Sanitization logic here for custom config options
     */
    private function sanitize_config_option(array $custom_config_options){
        return $custom_config_options;
    }

    private function unpack_media_breakpoints(array $config_options) {
        if (isset($config_options['media-breakpoints'])) {
            $media_breakpoints = $config_options['media-breakpoints'];
            foreach ($media_breakpoints as $media_breakpoint_alias => $media_breakpoint_config) {
                static::$media_breakpoints[$media_breakpoint_alias] = $media_breakpoint_config;
            }
            unset($config_options['media-breakpoints']);
        }
        
        return $config_options;
    }

}
