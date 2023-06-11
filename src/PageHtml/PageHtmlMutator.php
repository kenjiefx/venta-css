<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS\PageHtml;
use Kenjiefx\VentaCSS\Registries\ClassRegistry;

class PageHtmlMutator
{
    public function __construct(
        private ClassRegistry $ClassRegistry
    ){

    }

    public function mutate(string $page_html){
        foreach ($this->ClassRegistry->get_class_registry_index() as $class_registry_index) {
            if (strpos($page_html, $class_registry_index) !== false){
                $minified_class_names = $this->ClassRegistry->get_minified_class_names($class_registry_index);
                $class_attribute_minified = 'class="'.implode(' ',$minified_class_names).'"';
                $page_html = str_replace($class_registry_index,$class_attribute_minified,$page_html);
            }
        }
        return $page_html;
    }
}
