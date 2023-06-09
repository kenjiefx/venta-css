<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS\Utilities;
use Kenjiefx\VentaCSS\Registries\ClassRegistry;
use Kenjiefx\VentaCSS\Utilities\UtilityClassRegistry;

class UtilityClassCompiler
{

    public function __construct(
        private UtilityClassRegistry $UtilityClassRegistry,
        private ClassRegistry $ClassRegistry,
        private ClassNameMinifierService $ClassNameMinifierService
    ){

    }

    public function compile(){

        $utility_classes = $this->UtilityClassRegistry->register();
        foreach ($utility_classes as $utility_class => $utility_class_rules) {

            $minified_utility_class_name = null;

            foreach ($this->ClassRegistry->get_registered_space_separated_class_names() as $space_separated_class_names_from_registry) {

                # Minified class names found in the HTML page
                $minified_class_names = $this->ClassRegistry->get_minified_class_names($space_separated_class_names_from_registry);
                $array_of_class_names = $this->ClassRegistry->get_array_of_class_names($space_separated_class_names_from_registry);

                # If this utility class is being used in the HTML page 
                if (in_array($utility_class,$array_of_class_names)) {

                    # If this specific class name has not been given a minified name version
                    if ($minified_utility_class_name===null) {
                        $minified_utility_class_name = $this->ClassNameMinifierService->create_minified_name_token();
                    }

                    $updated_class_names = [];
                    foreach ($array_of_class_names as $class_name) {
                        
                    }

                }


                


            }
        }
    }
}
