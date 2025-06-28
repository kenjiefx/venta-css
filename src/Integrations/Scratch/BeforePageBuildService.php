<?php 

namespace Kenjiefx\VentaCSS\Integrations\Scratch;

use Kenjiefx\VentaCSS\Options\OptionsCollector;
use Kenjiefx\VentaCSS\Services\BreakpointRegistrationService;
use Kenjiefx\VentaCSS\Services\ClassRegistrationService;
use Kenjiefx\VentaCSS\Usages\ClassList\ClassListRegistry;

class BeforePageBuildService {

    public function __construct(
        public readonly OptionsCollector $optionsCollector,
        public readonly ClassRegistrationService $classRegistrationService,
        public readonly BreakpointRegistrationService $breakpointRegistrationService,
        public readonly ClassListRegistry $classListRegistry
    ) {}

    public function run(){
        $this->classListRegistry->clear();
        $options = $this->optionsCollector->collect();
        foreach ($options as $option) {
            $this->classRegistrationService->fromOption($option);
            $this->breakpointRegistrationService->fromOption($option);
        }
        $classRegistry = $this->classRegistrationService->getRegistry();
        // $classes = $classRegistry->getAll();
        // foreach ($classes as $class) {
        //     $classDeclaration = $class->declaration;
        //     $property = $class->key->property;
        //     $variant = $class->key->variant;
        //     echo "{$property}:{$variant} { {$classDeclaration} }<br>";
        // }
    }

}