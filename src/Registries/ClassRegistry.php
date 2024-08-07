<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS\Registries;

/**
 * Contains all the class names used in a HTML string, regardless
 * whether these class names are related to VentaCSS or not. 
 */
class ClassRegistry
{
    /** 
     * A list of class declarations. 
     * ['class="class names"'] = [
     *     'classNames'    => ['class','names'],
     *     'minifiedNames' => ['drF,'xB3']
     * ];
     */
    private static array $classes = [];

    private const CLASS_ATTRIBUTE = 'class="';

    /**
     * Parses and registers all classes in a given HTML string
     */
    public function register(string $htmls){
        if (empty(static::$classes)) {
            $attrs = str_split(Self::CLASS_ATTRIBUTE);
            $pointer = 0;
            $recording = false;
            $classes = '';
            foreach (str_split($htmls) as $htmlChar) {
                if ($recording && $htmlChar!=='"') {
                    $classes = $classes.$htmlChar;
                    continue;
                }
                if ($recording && $htmlChar==='"') {
                    $this->toRegistry($classes);
                    $classes = '';
                    $recording = false;
                    $pointer = 0;
                    continue;
                }
                ($htmlChar === $attrs[$pointer]) ? $pointer++ : $pointer = 0;
                if ($pointer===count($attrs)) {
                    $recording = true;
                }
            }
        }
    }

    /**
     * Adds item to the ClassRegistry::$classes array
     */
    private function toRegistry(string $space_separated_class_names){
        $registry_index_name = $this->to_registry_index_name($space_separated_class_names);
        static::$classes[$registry_index_name] = [
            'class_names'          => explode(' ',$space_separated_class_names),
            'minified_class_names' => explode(' ',$space_separated_class_names)
        ];
    }

    private function to_registry_index_name(string $space_separated_class_names){
        return 'class="'.$space_separated_class_names.'"';
    }

    /**
     * Validates whether a space-separated class names exists or has been 
     * registered in the Class Registry
     */
    private function validate_registry_index($class_registry_index){
        if (empty(static::$classes)||!isset(static::$classes[$class_registry_index])) {
            throw new \InvalidArgumentException('Missing class index');
        }
    }

    /** 
     * Returns class registry index, that is, an array of class declarations 
     * class="names" 
     */
    public function get_class_registry_index():array{
        $class_registry_index = [];
        foreach (static::$classes as $registry_index_name => $data) {
            array_push($class_registry_index, $registry_index_name);
        }
        return $class_registry_index;
    }

    public function get_array_of_class_names(string $class_registry_index):array{
        $this->validate_registry_index($class_registry_index);
        return static::$classes[$class_registry_index]['class_names'];
    }

    public function get_minified_class_names(string $class_registry_index):array{
        $this->validate_registry_index($class_registry_index);
        return static::$classes[$class_registry_index]['minified_class_names'];
    }

    public function set_array_of_class_names(string $class_registry_index,array $array_of_class_names){
        $this->validate_registry_index($class_registry_index);
        static::$classes[$class_registry_index]['class_names'] = $array_of_class_names;
    }

    public function set_minified_class_names(string $class_registry_index,array $array_of_class_names){
        $this->validate_registry_index($class_registry_index);
        static::$classes[$class_registry_index]['minified_class_names'] = $array_of_class_names;
    }

    public function clear_registry(){
        static::$classes = [];
    }
}
