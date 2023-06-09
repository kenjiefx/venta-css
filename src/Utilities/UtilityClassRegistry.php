<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS\Utilities;
use Kenjiefx\VentaCSS\VentaConfig;

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
                    throw new \InvalidArgumentException('Incorrect configuration. Missing required "type"');
                }

                switch($configuration['type']) {
                    
                    case 'minmax': 
                        # The difference of the values between the variants
                        [$min,$max] = $configuration['values'];
                        $variants   = $configuration['variants'];
                        $increment  = (intval($max) - intval($min)) / $variants;
                        $separator  = $configuration['separator'];
                        $rule       = $configuration['rule'];
                        $i = 0;
                        while($i<$variants){
                            # Generating the actual selector name
                            $actual_utility_selector = $attribute_name.$separator.($i+1);
                            $value = strval(round($min+$increment,3));
                            static::$array_of_utility_classes[$actual_utility_selector] = $this->fill_placeholder($rule,$value);
                            $min = $min+$increment;
                            $i++;
                        }
                        break;


                    case 'list':
                        $values = $configuration['values'];
                        $rule   = $configuration['rule'];
                        foreach ($values as $value) {
                            # Generating the actual selector name
                            $actual_utility_selector = $attribute_name.$separator.$value;
                            static::$array_of_utility_classes[$actual_utility_selector] = $this->fill_placeholder($rule,$value);
                        }
                        break;
                    default: 
                        $error = 'Invalid configuration.';
                        throw new \InvalidArgumentException($error);
                }

            }
        }
        return static::$array_of_utility_classes;
    }

    private function fill_placeholder(string $text, string $value){
        return str_replace(self::PLACEHOLDER,$value,$text);
    }
}
