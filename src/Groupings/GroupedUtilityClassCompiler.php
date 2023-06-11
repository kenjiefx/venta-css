<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS\Groupings;
use Kenjiefx\VentaCSS\Registries\ClassRegistry;

class GroupedUtilityClassCompiler
{

    public function __construct(
        private ClassRegistry $ClassRegistry,
        private GroupedUtilityClassRegistry $GroupedUtilityClassRegistry
    ){

    }

    public function compile(){

        foreach ($this->GroupedUtilityClassRegistry->register() as $group_name => $array_of_grouped_space_separated_class_names) {
            /**
             * @NOTE The variable "classes" in this context is the list/array of class names
             * that were declared part of the group referenced by "groupName"
             */
            foreach ($this->ClassRegistry->get_class_registry_index() as $class_registry_index) {

                # Minified class names found in the HTML page
                $array_of_class_names = $this->ClassRegistry->get_array_of_class_names($class_registry_index);

                # If the group name exists in the class names found in the HTML page
                if  (in_array($group_name, $array_of_class_names)) {

                    $updated_class_names = [];
                    foreach ($array_of_class_names as $class_name) {
                        /**
                         * At this point, we are subtituting group name into class names that are declared
                         * to be part of the group. For example
                         * 
                         * Input: 
                         * class="group-1 color-black"
                         * Output:  
                         * class="text-3 font-weight-4 color-black"
                         */
                        if ($group_name === $class_name) {
                            foreach ($array_of_grouped_space_separated_class_names as $class_name_derived_from_group) {
                                array_push($updated_class_names, $class_name_derived_from_group);
                            } 
                        } else {
                            array_push($updated_class_names, $class_name);
                        }
                    }

                    $this->ClassRegistry->set_array_of_class_names($class_registry_index, $updated_class_names);
                    $this->ClassRegistry->set_minified_class_names($class_registry_index, $updated_class_names);
                }

            }
        }
    }

    public function clear_grouped_utility_class_registry(){
        $this->GroupedUtilityClassRegistry->clear_registry();
    }

}
