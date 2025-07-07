<?php 

namespace Kenjiefx\VentaCSS\Variables;
use Kenjiefx\VentaCSS\Variables\RootVariableRegistry;
use Kenjiefx\VentaCSS\Classes\ClassModel;

class RootVariableService {

    /**
     * Static array to hold used variables across instances
     * This allows us to track which variables have been used. 
     * 
     * @var array<string, ClassModel>
     */
    private static array $usedVariables = [];

    public function __construct(
        public readonly RootVariableRegistry $rootVariableRegistry,
    ) {}

    public function collect(string $content) {
        $this->rootVariableRegistry->build();
        $cssVariables = $this->extractCssVariables($content);
        foreach ($cssVariables as $cssVariable) {
            if (!isset(static::$usedVariables[$cssVariable])) {
                $classModel = $this->rootVariableRegistry->getByKey($cssVariable);
                if ($classModel === null) continue;

                static::$usedVariables[$cssVariable] = $classModel;
            }
        }
    }

    /**
     * Collects CSS variables from the provided content
     * @param string $content The content to process.
     */
    private function extractCssVariables(string $content): array {
        preg_match_all('/var\(\s*(--[a-zA-Z0-9-_]+)\s*\)/', $content, $matches);
        return array_values(array_unique($matches[1]));
    }

    public function clearUsedVariables() {
        static::$usedVariables = [];
    }

    public function createRootCssVariables(): string {
        $css = ":root {\n";
        foreach (static::$usedVariables as $variableKey => $classModel) {
            $variableValue = trim(explode(':', $classModel->declaration)[1] ?? '');
            $css .= "{$variableKey}: {$variableValue};\n";
        }
        $css .= '}';
        return $css;
    }

    

}