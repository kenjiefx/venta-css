<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS\Groupings;
use Kenjiefx\ScratchPHP\App\Themes\ThemeController;

class GroupedUtilityClassRegistry
{
    /**
     * A dictionary of all grouped classes declared
     * in the directory GROUPED_CSS_JSON_DIR
     */
    private static array $groups = [];

    /**
     * The path to where all the grouped class declarations
     * are registered.
     */
    private const GROUPED_CSS_JSON_DIR = '/venta/css';

    public function __construct(
        private ThemeController $ThemeController
    ){
        
    }

    public function register()
    {
        if (empty(static::$groups)) {

            $grouped_class_storage_path = $this->ThemeController->getdir().Self::GROUPED_CSS_JSON_DIR;
            if (is_dir($grouped_class_storage_path)) {

                # Looping through all the files
                foreach (scandir($grouped_class_storage_path) as $grouped_class_json_file_name) {
                    if ($grouped_class_json_file_name==='.'||$grouped_class_json_file_name==='..') continue;

                    $file_name_tokens = explode('.',$grouped_class_json_file_name);

                    # File must have .json extension!
                    if ($file_name_tokens[count($file_name_tokens) - 1]!=='json') continue;

                    $this->collect_group(
                        $this->load_json_file($grouped_class_storage_path.'/'.$grouped_class_json_file_name)
                    );
                }
            }
        }
        return static::$groups;
    }

    private function load_json_file(string $grouped_class_json_file_path)
    {
        if (!file_exists($grouped_class_json_file_path)) return [];
        return json_decode(file_get_contents($grouped_class_json_file_path),TRUE);
    }

    private function collect_group(array $groups){
        foreach ($groups as $group) {
            foreach ($group as $name => $classes) {
                static::$groups[$name] = explode(' ',$classes);
            }
        }
    }

    public function clear_registry(){
        static::$groups = [];
    }
}
