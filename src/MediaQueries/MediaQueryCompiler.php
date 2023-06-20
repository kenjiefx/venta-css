<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS\MediaQueries;
use Kenjiefx\VentaCSS\Registries\ClassRegistry;
use Kenjiefx\VentaCSS\Utilities\ClassNameMinifierService;
use Kenjiefx\VentaCSS\Utilities\UtilityClassRegistry;

class MediaQueryCompiler
{

    /**
     * An array of media breakpoints and classes that are being utilized in a given
     * page HTML. The reason why this is not a static property, one
     * page might use media breakpoint declarations that aren't being used by other pages. 
     * Setting this as empty array makes sure that we only export
     * the umedia breakpoints used or utilized in a given page HTML.
     */
    private array $utilized_breakpoints_and_classes = [];

    private array $utilized_minfied_utility_class_names = [];


    private static array $prepared_breakpoint_addons = [];


    public function __construct(
        private MediaQueryRegistry $MediaQueryRegistry,
        private UtilityClassRegistry $UtilityClassRegistry,
        private ClassRegistry $ClassRegistry,
        private ClassNameMinifierService $ClassNameMinifierService
    ){

    }

    public function compile(){

        # Retrieving breakpoints declared in the config
        $this->MediaQueryRegistry->register();

        # Registering available utility classes
        $this->UtilityClassRegistry->register();

        # Looping through class declaration from the Class Registry
        foreach ($this->ClassRegistry->get_class_registry_index() as $class_registry_index) {

            # Looping through class names found in the HTML page
            $array_of_class_names = $this->ClassRegistry->get_array_of_class_names($class_registry_index);
            $updated_class_names = [];
            foreach ($array_of_class_names as $class_name_from_class_registry) {

                if (str_contains($class_name_from_class_registry,'@')) {
                    
                    /**
                     * At this point, we are trying to parse the media query declaration. 
                     * For example, display-none@mobile will be parse as {utility_class_name}@{breakpoint_alias}
                     */
                    [$utility_class_name,$breakpoint_alias] = explode('@',$class_name_from_class_registry);
                    $breakpoint_config = $this->MediaQueryRegistry->get_breakpoint_config_by_alias($breakpoint_alias);

                    # Removing the class name with @ if the breakpoint alias does not exist within the page HTML
                    if ($breakpoint_config===null) {
                        $class_name_from_class_registry = '';
                    } else {
                        $previously_utilized_minified_name = $this->get_utilized_minified_names($class_name_from_class_registry);
                        if ($previously_utilized_minified_name===null) {
                            $utility_class_value = $this->UtilityClassRegistry->get_utility_value($utility_class_name);
                            $minified_class_name = $this->ClassNameMinifierService->create_minified_name_token();
                            $this->push_to_utilized_minified_names($class_name_from_class_registry,$minified_class_name);
                            $class_name_from_class_registry = $minified_class_name;
                            $this->push_to_utilized_breakpoints($breakpoint_alias,$class_name_from_class_registry,$utility_class_value);
                        } else {
                            $class_name_from_class_registry = $previously_utilized_minified_name;
                        }
                    }

                }

                array_push($updated_class_names,$class_name_from_class_registry);

            }

            $this->ClassRegistry->set_array_of_class_names($class_registry_index, $updated_class_names);
            $this->ClassRegistry->set_minified_class_names($class_registry_index, $updated_class_names);
        }


    }

    private function push_to_utilized_breakpoints(string $breakpoint_alias,string $minified_utility_class_name,string $utility_class_value){
        if (!isset($this->utilized_breakpoints_and_classes[$breakpoint_alias])) {
            $this->utilized_breakpoints_and_classes[$breakpoint_alias] = [];
        }
        if (!isset($this->utilized_breakpoints_and_classes[$breakpoint_alias][$minified_utility_class_name])){
            $this->utilized_breakpoints_and_classes[$breakpoint_alias][$minified_utility_class_name] = $utility_class_value;
        }
    }

    private function push_to_utilized_minified_names(string $class_name_from_class_registry, string $minified_class_name_version){
        $this->utilized_minfied_utility_class_names[$class_name_from_class_registry] = $minified_class_name_version;
    }

    private function get_utilized_minified_names(string $class_name_from_class_registry){
        return $this->utilized_minfied_utility_class_names[$class_name_from_class_registry] ?? null;
    }

    public function clear_utilized_breakpoints_list(){
        $this->utilized_breakpoints_and_classes = [];
        $this->utilized_minfied_utility_class_names = [];
        static::$prepared_breakpoint_addons = [];
    }

    /**
     * Exports Media query breakpoints as CSS string
     */
    public function to_exportable_css(){
        $css = '';
        $used_breakpoints = [];
        foreach ($this->utilized_breakpoints_and_classes as $breakpoint_alias => $breakpoint_classes) {
            $derived_condition = $this->MediaQueryRegistry->get_breakpoint_config_by_alias($breakpoint_alias)['derived_condition'];
            if (!isset($used_breakpoints[$derived_condition])) {
                $used_breakpoints[$derived_condition] = [];
            }
            foreach ($breakpoint_classes as $minified_utility_class_name => $value) {
                array_push(
                    $used_breakpoints[$derived_condition],
                    '.'.$minified_utility_class_name.'{'.$value.'}'
                );
            }
        }
        foreach (static::$prepared_breakpoint_addons as $derived_condition => $css_statements) {
            if (!isset($used_breakpoints[$derived_condition])) {
                $used_breakpoints[$derived_condition] = [];
            }
            foreach ($css_statements as $css_statement) {
                array_push(
                    $used_breakpoints[$derived_condition],
                    $css_statement
                );
            }
        }
        foreach ($used_breakpoints as $derived_condition => $css_statements) {
            $css .= $derived_condition.'{';
            foreach ($css_statements as $css_statement) {
                $css .= $css_statement;
            }
            $css .= '}';      
        }
        return $css;
    }

    public static function set_prepared_breakpoint_addon(string $breakpoint_derived_condition, string $css_statement){
        if (!isset(static::$prepared_breakpoint_addons[$breakpoint_derived_condition])) {
            static::$prepared_breakpoint_addons[$breakpoint_derived_condition] = [];
        }
        array_push(static::$prepared_breakpoint_addons[$breakpoint_derived_condition],$css_statement);
    }
}
