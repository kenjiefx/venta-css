<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS\Utilities;
use Kenjiefx\VentaCSS\VentaConfig;

/**
 * A Registry of all the built-in and custom-defined utility classes
 */
class UtilityClassRegistry
{

    private static array $array_of_utility_classes = [];

    private const PLACEHOLDER = '{value}';

    public function __construct(
        private VentaConfig $VentaConfig
    ){

    }

    public function register(){
        if (empty(static::$array_of_utility_classes)) {

            # Retrieves all the attribute names declared in the config, and looping through them
            $array_of_attribute_names_from_config = $this->VentaConfig->get_attribute_list();
            foreach ($array_of_attribute_names_from_config as $attribute_name) {

                $configuration = $this->VentaConfig->get_attribute($attribute_name);
                if (!isset($configuration['type'])) {
                    throw new \InvalidArgumentException('Incorrect configuration. Missing required field "type"');
                }

                switch($configuration['type']) {
                    
                    case 'minmax': 
                        # The difference of the values between the variants
                        [$min,$max]  = $configuration['values'];
                        $variants    = $configuration['variants'];
                        $numeric_min = (str_contains($min,'.')) ? floatval($min) : intval($min);
                        $numeric_max = (str_contains($max,'.')) ? floatval($max) : intval($max);
                        $increment   = ($numeric_max-$numeric_min) / intval($variants);
                        $separator   = $configuration['separator'];
                        $rule        = $configuration['rule'];

                        $i = 0;
                        while($i<$variants){
                            # Generating the actual selector name
                            $actual_utility_selector = $attribute_name.$separator.($i+1);
                            
                            $value = strval(round($min+$increment,3));
                            static::$array_of_utility_classes[$actual_utility_selector] = [
                                'value' => $this->fill_placeholder($rule,$value),
                                'minified_name' => null
                            ];
                            $min = $min+$increment;
                            $i++;
                        }
                        break;


                    case 'list':
                        $values = $configuration['values'];
                        $rule   = $configuration['rule'];
                        $separator   = $configuration['separator'];
                        foreach ($values as $value) {
                            # Generating the actual selector name
                            $actual_utility_selector = $attribute_name.$separator.$value;
                            static::$array_of_utility_classes[$actual_utility_selector] = [
                                'value' => $this->fill_placeholder($rule,$value),
                                'minified_name' => null
                            ];
                        }
                        break;


                    case 'dictionary': 
                        $values = $configuration['values'];
                        $rule = $configuration['rule'];
                        $separator = $configuration['separator'];
                        $themed_keyval = [];
                        $themes = $configuration['themes'] ?? [];
                        foreach ($themes as $theme_name => $theme_configs) {
                            foreach($theme_configs['values'] as $theme_key => $theme_value) {
                                $themed_keyval[$theme_key] = [
                                    'value' => $theme_value,
                                    'theme_name' => $theme_name
                                ];
                            }
                        }
                        foreach ($values as $key => $value) {
                            # Generating the actual selector name
                            $actual_utility_selector = $attribute_name.$separator.$key;
                            $themed_keyval_data = [];
                            if (isset($themed_keyval[$key])) {
                                $themed_keyval_data = [
                                    'value' => $this->fill_placeholder($rule,$themed_keyval[$key]['value']),
                                    'theme_name' => $themed_keyval[$key]['theme_name']
                                ];
                            }
                            static::$array_of_utility_classes[$actual_utility_selector] = [
                                'value' => $this->fill_placeholder($rule,$value),
                                'minified_name' => null,
                                'themed_keyval' => $themed_keyval_data
                            ];
                        }
                        break;   


                    case 'count';
                        # The difference of the values between the variants
                        [$min,$max] = $configuration['values'];
                        $rule       = $configuration['rule'];
                        $separator   = $configuration['separator'];
                        $iterator   = 1;
                        while ($iterator<intval($max)+1) {
                            # Generating the actual selector name
                            $actual_utility_selector = $attribute_name.$separator.$iterator;
                            static::$array_of_utility_classes[$actual_utility_selector] = [
                                'value' => $this->fill_placeholder($rule,strval($iterator)),
                                'minified_name' => null
                            ];
                            $iterator++;
                        }
                        break;


                    default: 
                        $error = 'Invalid configuration. Unknown venta config type.';
                        throw new \InvalidArgumentException($error);
                }

            }
        }
        return static::$array_of_utility_classes;
    }

    private function fill_placeholder(string $text, string $value){
        return str_replace(self::PLACEHOLDER,$value,$text);
    }


    public function set_minified_name(string $utility_class_name, string $minified_class_name){
        static::$array_of_utility_classes[$utility_class_name]['minified_name'] = $minified_class_name;
    }

    public function get_registry(){
        return static::$array_of_utility_classes;
    }

    public function get_minified_name(string $utility_class_name){
        return static::$array_of_utility_classes[$utility_class_name]['minified_name'];
    }

    public function get_utility_value(string $utility_class_name){
        return static::$array_of_utility_classes[$utility_class_name]['value'];
    }

    public function get_utility_themed_keyval(string $utility_class_name){
        return static::$array_of_utility_classes[$utility_class_name]['themed_keyval'] ?? [];
    }

    public function clear_registry(){
        static::$array_of_utility_classes = [];
    }
}
