<?php 

namespace Kenjiefx\VentaCSS\Services;

use Kenjiefx\VentaCSS\Breakpoints\BreakpointFactory;
use Kenjiefx\VentaCSS\Breakpoints\BreakpointRegistry;
use Kenjiefx\VentaCSS\Options\OptionModel;
use Kenjiefx\VentaCSS\Options\OptionType;

class BreakpointRegistrationService {

    public function __construct(
        public readonly BreakpointFactory $breakpointFactory,
        public readonly BreakpointRegistry $breakpointRegistry
    ) {}

    public function fromOption(
        OptionModel $optionModel
    ) {
        if ($optionModel->type !== OptionType::breakpoint) {
            return null; // Only process breakpoint options
        }
        $name = $optionModel->property;
        $mediaQuery = $this->fillPlaceholder(
            rule: $optionModel->rule, 
            minWidth: $optionModel->values["min-width"], 
            maxWidth: $optionModel->values["max-width"]
        );
        $breakpointModel = $this->breakpointFactory->create(
            name: $name,
            mediaQuery: $mediaQuery
        );
        $this->breakpointRegistry->register(
            name: $breakpointModel->name,
            breakpoint: $breakpointModel
        );
    }

    public function fillPlaceholder(
        string $rule, 
        string $minWidth, 
        string $maxWidth
    ): string {
        $step1 = str_replace(
            "{minWidth}", $minWidth, $rule
        );
        return str_replace(
            "{maxWidth}", $maxWidth, $step1
        );
    }

}