<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS\PseudoClass;
use Kenjiefx\VentaCSS\MediaQueries\MediaQueryCompiler;
use Kenjiefx\VentaCSS\MediaQueries\MediaQueryRegistry;
use Kenjiefx\VentaCSS\Registries\ClassRegistry;
use Kenjiefx\VentaCSS\Utilities\ClassNameMinifierService;
use Kenjiefx\VentaCSS\Utilities\UtilityClassRegistry;

class PseudoClassCompiler
{

    private array $utilized_pseudo_class = [];

    public function __construct(
        private UtilityClassRegistry $UtilityClassRegistry,
        private ClassRegistry $ClassRegistry,
        private ClassNameMinifierService $ClassNameMinifierService,
        private MediaQueryRegistry $MediaQueryRegistry
    ){

    }
    public function compile(){

        # Registering available utility classes
        $this->UtilityClassRegistry->register();

        $this->MediaQueryRegistry->register();

        # Looping through class declaration from the Class Registry
        foreach ($this->ClassRegistry->get_class_registry_index() as $registry_index) {

            # Looping through class names found in the HTML page
            $classes_array   = $this->ClassRegistry->get_array_of_class_names($registry_index);
            $updated_classes = [];

            foreach ($classes_array as $class_name) {
                $updated_class_name = $class_name;

                if (str_contains($class_name,':')) {

                    [$utility_name,$pseudo_class] = explode(':',$class_name);

                    $breakpoint_condition = null;

                    if (str_contains($pseudo_class,'@')) {
                        [$pseudo_class, $breakpoint_alias] = explode('@',$pseudo_class);
                        $breakpoint_condition = $this->MediaQueryRegistry->get_breakpoint_config_by_alias($breakpoint_alias)['derived_condition'];
                    }

                    $prev_utilized_pseudo = $this->get_prev_utilized_pseudo($class_name);

                    # If there aren't any previously-utilized registered pseduos
                    if (null===$prev_utilized_pseudo) {

                        $minified_name       = $this->ClassNameMinifierService->create_minified_name_token();
                        $utility_class_value = $this->UtilityClassRegistry->get_utility_value($utility_name);

                        $this->set_utilized_pseudo(
                            utility_class_name: $utility_name,
                            class_name:    $class_name,
                            minified_name: $minified_name,
                            css_value:     $utility_class_value,
                            pseudo_class:  $pseudo_class,
                            breakpoint_condition: $breakpoint_condition
                        );
                        $updated_class_name = $minified_name;

                    } else {
                        $updated_class_name = $prev_utilized_pseudo['minified_name'];
                    }
                }
                array_push($updated_classes,$updated_class_name);
            }

            $this->ClassRegistry->set_array_of_class_names($registry_index, $updated_classes);
            $this->ClassRegistry->set_minified_class_names($registry_index, $updated_classes);
        }
    }

    public function get_prev_utilized_pseudo(string $class_name){
        if (!isset($this->utilized_pseudo_class[$class_name])) return null;
        return $this->utilized_pseudo_class[$class_name];
    }

    public function set_utilized_pseudo(string $utility_class_name, string $class_name, string $minified_name, string $css_value, string $pseudo_class, ?string $breakpoint_condition){
        $this->utilized_pseudo_class[$class_name] = [
            'minified_name' => $minified_name,
            'css_value' => $css_value,
            'pseudo_class' => $pseudo_class,
            'breakpoint_condition' => $breakpoint_condition,
            'utility_class_name' => $utility_class_name
        ];
    }

    public function clear(){
        $this->utilized_pseudo_class = [];
    }

    public function export(){
        $css = '';
        foreach ($this->utilized_pseudo_class as $class_name => $data) {
            $themed_keyval = $this->UtilityClassRegistry->get_utility_themed_keyval($data['utility_class_name']);
            if (!empty($themed_keyval)) {
                $css .= '.'.$themed_keyval['theme_name'].' .'.$data['minified_name'].':'.$data['pseudo_class'].'{'.$themed_keyval['value'].'}';
            }
            if (null===$data['breakpoint_condition']) {
                $css .= '.'.$data['minified_name'].':'.$data['pseudo_class'].'{'.$data['css_value'].'}';
                continue;
            }
            $breakpoint_condition = $data['breakpoint_condition'];
            MediaQueryCompiler::set_prepared_breakpoint_addon(
                breakpoint_derived_condition: $breakpoint_condition, 
                css_statement: '.'.$data['minified_name'].':'.$data['pseudo_class'].'{'.$data['css_value'].'}'
            );
            if (!empty($themed_keyval)) { 
                MediaQueryCompiler::set_prepared_breakpoint_addon(
                    breakpoint_derived_condition: $breakpoint_condition, 
                    css_statement: '.'.$themed_keyval['theme_name'].' .'.$data['minified_name'].':'.$data['pseudo_class'].'{'.$themed_keyval['value'].'}'
                );
            }
        }
        return $css;
    }
}
