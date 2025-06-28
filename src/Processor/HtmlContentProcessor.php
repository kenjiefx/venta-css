<?php 

namespace Kenjiefx\VentaCSS\Processor;

use Kenjiefx\VentaCSS\Processor\ClassAttributes\ClassAttributeCollector;
use Kenjiefx\VentaCSS\Processor\StyleNotations\StyleNotationFactory;
use Kenjiefx\VentaCSS\Processor\StyleNotations\StyleNotationRegistry;

class HtmlContentProcessor {

    public function __construct(
        public readonly ClassAttributeCollector $classAttributeCollector,
        public readonly StyleNotationFactory $styleNotationFactory,
        public readonly StyleNotationRegistry $styleNotationRegistry
    ) {}
    
    /**
     * Process the HTML content to minify class attributes.
     *
     * @param string $html The HTML content to process.
     * @return string The processed HTML content with minified class attributes.
     */
    public function processHtml(string $html): string {
        // Clear existing style notations in the registry
        $this->styleNotationRegistry->clearExisting();
        $classAttributes = $this->classAttributeCollector->collect($html);
        foreach ($classAttributes as $classAttribute) {
            $styleNotations = explode(" ", $classAttribute->value);
            $minifiedNames = [];
            foreach ($styleNotations as $styleNotation) {
                $styleNotationModel = $this->styleNotationFactory->create($styleNotation);
                // Register the style notation in the registry
                $this->styleNotationRegistry->registerIfNotExist($styleNotationModel);
                $registeredStyleNotationModel = $this->styleNotationRegistry->lookupByNotation($styleNotation);
                $minifiedNames[] = $registeredStyleNotationModel->minifiedName;
            }
            $minifiedclassAttribute = implode(" ", $minifiedNames);
            // Replace the class attribute value in the HTML with the minified version
            $html = $this->replaceClassAttributeValue(
                $html,
                $classAttribute->value,
                $minifiedclassAttribute
            );
        }
        // Output the processed HTML
        return $html;
    }

    public function exportCss() {
        $breakpoints = [];
        $registeredStyleNotationModels = $this->styleNotationRegistry->getAll();
        foreach ($registeredStyleNotationModels as $styleNotificationModel) {
            $breakpointModel = $styleNotificationModel->breakpoint;
            $breakpoint = ($breakpointModel === null) ? "default" : $breakpointModel->name;
            if (!isset($breakpoints[$breakpoint])) {
                $breakpoints[$breakpoint] = [
                    "model" => $styleNotificationModel->breakpoint,
                    "cssRegisters" => []
                ];
            }
            foreach ($styleNotificationModel->cssRegisters as $cssRegister) {
                $breakpoints[$breakpoint]["cssRegisters"][] = $cssRegister;
            }
        }
        $cssOutput = "";
        foreach ($breakpoints as $breakpoint => $breakpointData) {
            if ($breakpoint === "default") {
                // No breakpoint, just output the CSS registers
                foreach ($breakpointData["cssRegisters"] as $cssRegister) {
                    $cssOutput .= $cssRegister . "\n";
                }
                continue;
            }
            // Output the CSS registers for the specific breakpoint
            $breakpointModel = $breakpointData["model"];
            $mediaQueryDeclaration = $breakpointModel->mediaQuery;
            $cssOutput .= "{$mediaQueryDeclaration} {\n";
            foreach ($breakpointData["cssRegisters"] as $cssRegister) {
                $cssOutput .= "    " . $cssRegister . "\n";
            }
            $cssOutput .= "}\n";
        }
        // Return the CSS output
        return $cssOutput;
    }

    /**
     * Replace the class attribute value in the HTML with the minified version.
     *
     * @param string $html The HTML content to process.
     * @param string $originalClassAttribute The original class attribute value.
     * @param string $minifiedClassAttribute The minified class attribute value.
     * @return string The HTML content with the class attribute replaced.
     */
    private function replaceClassAttributeValue(
        string $html,
        string $originalClassAttribute,
        string $minifiedClassAttribute
    ): string {
        // Replace the class attribute value in the HTML with the minified version
        return str_replace(
            'class="' . $originalClassAttribute . '"',
            'class="' . $minifiedClassAttribute . '"',
            $html
        );
    }


}