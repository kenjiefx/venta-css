<?php 

namespace Kenjiefx\VentaCSS\Services;

use Kenjiefx\VentaCSS\Classes\ClassFactory;
use Kenjiefx\VentaCSS\Classes\ClassRegistry;
use Kenjiefx\VentaCSS\Options\OptionModel;
use Kenjiefx\VentaCSS\Options\OptionType;

class ClassRegistrationService {

    public const PLACEHOLDER = '{value}';

    public function __construct(
        public readonly ClassRegistry $classRegistry,
        public readonly ClassFactory $classFactory
    ) {}

    public function fromOption(
        OptionModel $optionModel
    ){
        return match ($optionModel->type) {
            OptionType::list => $this->processListType($optionModel),
            OptionType::count => $this->processCountType($optionModel),
            OptionType::minmax => $this->processMinMaxType($optionModel),
            OptionType::dictionary => $this->processDictionaryType($optionModel),
            // do nothing for default
            default => null
        };
    }

    /**
     * Processes a list type option model and registers utility classes.
     * @return void
     */
    public function processListType(OptionModel $optionModel){
        foreach ($optionModel->values as $value) {
            // Create utility class for each value in the list
            $declaration = $this->fillPlaceholder(
                $optionModel->rule, $value
            );
            $classModel = $this->classFactory->create(
                theme: $optionModel->theme,
                property: $optionModel->property,
                declaration: $declaration,
                variant: $value
            );
            $this->classRegistry->register(
                $classModel->key, $optionModel->theme, $classModel
            );
        }
    }

    /**
     * Processes a min-max type option model and registers utility classes.
     * This method generates a range of values based on the min and max values,
     * and creates utility classes for each value in that range.
     * 
     * @example 
     * property: width 
     * min: 10px / max: 100px
     * This will create utility classes for width from 10px to 100px in intervals of 24 steps.
     * 
     * @return void
     */
    public function processMinMaxType(OptionModel $optionModel) {
        $min = $optionModel->values[0];
        $max = $optionModel->values[1];
        $interval = ($max - $min)/24; // Calculate interval for 24 steps
        $variant = 1;
        // Loop variant from 0 to 24
        for ($variant = 0; $variant <= 24; $variant++) {
            // Calculate the current value based on the variant
            $currentValue = $min + ($variant * $interval);
            // Create utility class for each value in the range
            $declaration = $this->fillPlaceholder(
                $optionModel->rule, (string)$currentValue
            );
            $classModel = $this->classFactory->create(
                theme: $optionModel->theme,
                property: $optionModel->property,
                declaration: $declaration,
                variant: $variant
            );
            $this->classRegistry->register(
                $classModel->key, $optionModel->theme, $classModel
            );
        }
    }

    public function processCountType(OptionModel $optionModel){
        $maxCount = 24; // Default maximum count
        $countFrom = $optionModel->values[0];
        $countTo = $optionModel->values[1];
        // create a loop from countFrom to countTo, but limit to 24
        for ($i = $countFrom; $i <= $countTo && $i <= $maxCount; $i++) {
            // Create utility class for each count
            $declaration = $this->fillPlaceholder(
                $optionModel->rule, (string)$i
            );
            $classModel = $this->classFactory->create(
                theme: $optionModel->theme,
                property: $optionModel->property,
                declaration: $declaration,
                variant: $i
            );
            $this->classRegistry->register(
                $classModel->key, $optionModel->theme, $classModel
            );
        }
    }

    public function processDictionaryType(OptionModel $optionModel) {
        foreach ($optionModel->values as $dictKey => $value) {
            // Create utility class for each value in the list
            $declaration = $this->fillPlaceholder(
                $optionModel->rule, $value
            );
            $classModel = $this->classFactory->create(
                theme: $optionModel->theme,
                property: $optionModel->property,
                declaration: $declaration,
                variant: $dictKey
            );
            $this->classRegistry->register(
                $classModel->key, $optionModel->theme, $classModel
            );
        }
    }

    public function getRegistry(): ClassRegistry {
        return $this->classRegistry;
    }

    public function fillPlaceholder(
        string $rule,
        string $value
    ) {
        return str_replace(self::PLACEHOLDER, $value, $rule);
    }

}