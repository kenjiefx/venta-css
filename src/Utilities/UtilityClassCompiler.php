<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS\Utilities;
use Kenjiefx\VentaCSS\Registries\ClassRegistry;
use Kenjiefx\VentaCSS\Utilities\UtilityClassRegistry;

class UtilityClassCompiler
{

    /**
     * A list of utility classes that are being utilized in a given
     * page HTML. The reason why this is not a static property, one
     * page might use utility class that aren't being used by other pages. 
     * Setting this as empty array makes sure that we only export
     * the utility class used or utilized in a given page HTML.
     */
    private array $utilized_utility_classes = [];


    public function __construct(
        private UtilityClassRegistry $UtilityClassRegistry,
        private ClassRegistry $ClassRegistry,
        private ClassNameMinifierService $ClassNameMinifierService
    ){

    }

    public function compile(){

        #echo json_encode($this->ClassRegistry->get_class_registry_index(),JSON_PRETTY_PRINT);

        $utility_classes = $this->UtilityClassRegistry->register();
        foreach ($utility_classes as $utility_class_name => $utility_class) {

            $minified_utility_class_name = null;

            foreach ($this->ClassRegistry->get_class_registry_index() as $class_registry_index) {

                # Class names found in the HTML page
                $array_of_class_names = $this->ClassRegistry->get_array_of_class_names($class_registry_index);

                # If this utility class is being used in the HTML page 
                if (in_array($utility_class_name, $array_of_class_names)) {

                    # If this specific class name has not been given a minified name version
                    if ($minified_utility_class_name===null) {
                        $minified_utility_class_name = $this->ClassNameMinifierService->create_minified_name_token();
                        $this->UtilityClassRegistry->set_minified_name($utility_class_name, $minified_utility_class_name);
                    }

                    /**
                     * Here, we are subtituting utility_class_name with the minified_class_name
                     * in the class registry.
                     * 
                     * Input:
                     * class="width-24 line-height-23"
                     * Output:
                     * class="rYz pQ4"
                     */
                    $updated_class_names = [];
                    foreach ($array_of_class_names as $class_name) {
                        if ($utility_class_name === $class_name) {
                            $class_name = $minified_utility_class_name;
                        } 
                        array_push($updated_class_names, $class_name);
                    }

                    $this->push_to_utilized_utility_class($utility_class_name);

                    $this->ClassRegistry->set_array_of_class_names($class_registry_index, $updated_class_names);
                    $this->ClassRegistry->set_minified_class_names($class_registry_index, $updated_class_names);

                }
            }
        }
    }

    /**
     * Export all the used utility classes into a CSS string
     * 
     * How do we determine if a utility class in used in the page?
     * Notice
     */
    public function to_exportable_css():string{
        $css = '';
        foreach ($this->utilized_utility_classes as $utilized_utility_class_name) {
            $minified_name = $this->UtilityClassRegistry->get_minified_name($utilized_utility_class_name);
            $value = $this->UtilityClassRegistry->get_utility_value($utilized_utility_class_name);
            $css .= '.'.$minified_name.'{'.$value.'}';
            $themed_keyval = $this->UtilityClassRegistry->get_utility_themed_keyval($utilized_utility_class_name);
            if (!empty($themed_keyval)) {
                $theme_name = $themed_keyval['theme_name'];
                $themed_value = $themed_keyval['value'];
                $css .= '.'.$theme_name.' .'.$minified_name.'{'.$themed_value.'}';
            }
        }
        return $css;
    }

    private function push_to_utilized_utility_class(string $utility_class_name){
        if (!in_array($utility_class_name,$this->utilized_utility_classes)) {
            array_push($this->utilized_utility_classes,$utility_class_name);
        }
    }

    public function clear_utility_class_registry(){
        $this->utilized_utility_classes = [];
        $this->UtilityClassRegistry->clear_registry();
    }
}
