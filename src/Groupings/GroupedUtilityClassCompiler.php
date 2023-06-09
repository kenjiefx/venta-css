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
            foreach ($this->ClassRegistry->get_registered_space_separated_class_names() as $space_separated_class_names_from_registry) {

                # Minified class names found in the HTML page
                $minified_class_names = $this->ClassRegistry->get_minified_class_names($space_separated_class_names_from_registry);
                $array_of_class_names = $this->ClassRegistry->get_array_of_class_names($space_separated_class_names_from_registry);

                # If the group name exists in the class names found in the HTML page
                if  (in_array($group_name,$array_of_class_names)) {
                    $updated_class_names = [];
                    foreach ($minified_class_names as $minified_class_name) {
                        if ($minified_class_name===$group_name) {
                            
                        } else {

                        }
                    }

                    $this->ClassRegistry->set_minified_class_names($space_separated_class_names_from_registry,$updated_class_names);
                }

            }
        }
    }

}
