<?php 

namespace Kenjiefx\VentaCSS\Processor\StyleNotations;

use Kenjiefx\VentaCSS\Breakpoints\BreakpointModel;
use Kenjiefx\VentaCSS\Breakpoints\BreakpointRegistry;
use Kenjiefx\VentaCSS\Classes\ClassRegistry;
use Kenjiefx\VentaCSS\Processor\PseudoClasses\PseudoClassEnum;
use Kenjiefx\VentaCSS\Tokens\MinifiedTokenPool;

class StyleNotationFactory {

    public function __construct(
        public readonly BreakpointRegistry $breakpointRegistry,
        public readonly MinifiedTokenPool $minifiedTokenPool,
        public readonly ClassRegistry $classRegistry
    ) {}

    /**
     * Converts style notation in string form into a StyleNotationModel.
     * @example: 
     * - "display:flex" becomes a StyleNotationModel with value "display:flex", minifiedName "ox7", and cssRegister ".ox7{display:flex;}".
     * - "color:blue:hover" becomes a StyleNotationModel with value "color:blue:hover", pseudoclassenum PseudoClassEnum:hover, minifiedName "b9m", and cssRegister ".b9m:hover{color:blue;}".
     * - "display:none@desktop" becomes a StyleNotationModel with value "display:none@desktop", breakpoint "desktop", minifiedName "d9k", and cssRegister ".d9k{display:none;}".
     * Notations without breakpoint or pseudo-class are also supported. 
     * Notations without breakpoint will default to the "default" breakpoint.
     * @param string $styleNotation
     * @return void
     */
    public function create(
        string $styleNotation
    ): StyleNotationModel {
        // Let's attempt to retrieve the breakpoint from the style notation.
        $breakpointModel = $this->retrieveBreakpoint($styleNotation);
        $pseudoClassEnum = $this->retrievePseudoClass($styleNotation);
        $minifiedName = $this->minifiedTokenPool->generate();
        $cssRegisters = $this->createCssRegisters(
            styleNotation: $styleNotation,
            minifiedName: $minifiedName
        );
        // Create the StyleNotationModel with the parsed values.
        return new StyleNotationModel(
            value: $styleNotation,
            minifiedName: $minifiedName,
            pseudoClass: $pseudoClassEnum,
            breakpoint: $breakpointModel,
            cssRegisters: $cssRegisters
        );
    }

    /**
     * Retrieves the breakpoint from the style notation if it exists.
     * If the notation contains a breakpoint, it will return the corresponding BreakpointModel.
     * If no breakpoint is found, it returns null.
     * @param string $styleNotation
     * @return BreakpointModel|null
     */
    private function retrieveBreakpoint(
        string $styleNotation
    ): ?BreakpointModel {
        if (str_contains($styleNotation, '@')) {
            $parts = explode('@', $styleNotation);
            $breakpoint = $parts[1];
            $breakpointModel = $this->breakpointRegistry->getByName($breakpoint);
            if ($breakpointModel === null) {
                throw new \InvalidArgumentException("Breakpoint '$breakpoint' is not registered.");
            }
            return $breakpointModel;
        }
        return null;
    }

    /**
     * Retrieves the pseudo-class from the style notation if it exists.
     * If the notation contains a pseudo-class, it will return the corresponding PseudoClassEnum.
     * If no pseudo-class is found, it returns null.
     * @param string $styleNotation
     * @return PseudoClassEnum|null
     */
    private function retrievePseudoClass(
        string $styleNotation
    ): ?PseudoClassEnum {
        $parts = explode(':', $styleNotation);
        if (count($parts) < 3)  return null;
        $pseudoClassPart = array_pop($parts);
        $pseudoClassPart = explode("@", $pseudoClassPart)[0]; // Remove any breakpoint if present
        $enumValue = ":{$pseudoClassPart}";
        $enum = PseudoClassEnum::fromString($enumValue);
        if (!$enum) {
            throw new \InvalidArgumentException("Invalid pseudo-class '$pseudoClassPart' in style notation '$styleNotation'.");
        }
        return $enum;
    }

    /**
     * Retrieves the utility class from the style notation.
     * The utility class is the part before the pseudo-class or breakpoint.
     * For example, "display:flex" becomes "display:flex", "color:blue:hover" becomes "color:blue", and "display:none@desktop" becomes "display:none".
     * @param string $styleNotation
     * @return string
     */
    private function retrieveUtilityClass(
        string $styleNotation
    ): string {
        $parts = explode(':', $styleNotation);
        if (count($parts) < 2) {
            throw new \InvalidArgumentException("Invalid style notation '$styleNotation'. Expected format is 'property:value' or 'property:value:pseudo-class' or 'property:value@breakpoint'.");
        }
        // If the notation contains a pseudo-class or breakpoint, we need to remove it.
        $parts[1] = explode("@", $parts[1])[0];
        return "{$parts[0]}:{$parts[1]}";
    }

    /**
     * Parses the property and value from the style notation.
     * For example, "display:flex" becomes ["display", "flex"], "color:blue:hover" becomes ["color", "blue"], and "display:none@desktop" becomes ["display", "none"].
     * @param string $styleNotation
     * @return array
     */
    private function parsePropertyAndValue(
        string $styleNotation
    ): array {
        $utilityClass = $this->retrieveUtilityClass($styleNotation);
        $parts = explode(':', $utilityClass);
        return [$parts[0], $parts[1]];
    }


    private function createCssRegisters(
        string $styleNotation,
        string $minifiedName,
    ): array {
        $cssRegisters = [];
        [$property, $value] = $this->parsePropertyAndValue($styleNotation);
        $pseudoClass = $this->retrievePseudoClass($styleNotation);
        $arrayOfClassModels = $this->classRegistry->lookup(
            property: $property,
            variant: $value
        );
        foreach ($arrayOfClassModels as $classModel) {
            $declaration = $classModel->declaration;
            $theme = $classModel->key->theme;
            $theme = $theme === "default" ? "" : ".{$theme}";
            $pseudClassString = $pseudoClass ? $pseudoClass->toString() : "";
            $className = ".{$minifiedName}{$pseudClassString}";
            // trim in case theme is empty
            $cssRegisters[] = trim("{$theme} {$className}{{$declaration}}");
        }
        return $cssRegisters;
    }

}