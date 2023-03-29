<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS;
use Kenjiefx\ScratchPHP\App\Components\ComponentModel;
use Kenjiefx\ScratchPHP\App\Interfaces\ExtensionsInterface;
use Kenjiefx\VentaCSS\VentaConfig;
use Kenjiefx\VentaCSS\Services\ClassParser;
use Kenjiefx\VentaCSS\Services\AssetsManager;
use Kenjiefx\VentaCSS\Services\ClassNameMinifier;
use Kenjiefx\VentaCSS\Services\MediaQueryManager;
use Kenjiefx\VentaCSS\Factories\VentaConfigFactory;
use Kenjiefx\VentaCSS\Services\GroupedAssetsManager;
use Kenjiefx\VentaCSS\Services\ThemeCustomAssetsManager;


class VentaCSS implements ExtensionsInterface {

    /**
     * @var string $PreProcessedHTML - The HTML document
     *             before CSS file has been processed
     */
    private string $PreProcessedHTML = '';

    /**
     * @var string $PostProcessedHTML - The HTML document
     *             after CSS file has been processed
     */
    private string $PostProcessedHTML = '';

    /**
     * @var array $ClassRegistry - Contains all the class attributes
     *            that can be found in the source the HTML document
     */
    private array $ClassRegistry = [];

    /**
     * @var array $ListOfUtilityClasses - All Utility classes generated
     *            by the AssetsManager object
     */
    private array $ListOfUtilityClasses = [];

    /**
     * All Utility classes that were being used, already minified
     */
    private array $UsedUtilityClasses = [];

    /**
     * All media breakpoints and their Utility classes generated 
     * and listed by the MediaQueryManager object
     */
    private array $ListOfMediaQueryBreakPoints = [];

    /**
     * All media breakpoints and their Utility Classes being used
     */
    private array $UsedMediaQueryBreakpoints = [];

    private static bool $hasLoaded;


    private VentaConfig $VentaConfig;


    public function __construct(
        private VentaConfigFactory $VentaConfigFactory,
        private ClassParser $ClassParser,
        private AssetsManager $AssetsManager,
        private ThemeCustomAssetsManager $ThemeCustomAssetsManager,
        private GroupedAssetsManager $GroupedAssetsManager,
        private ClassNameMinifier $ClassNameMinifier,
        private MediaQueryManager $MediaQueryManager
        )
    {
        $this->VentaConfig = VentaConfigFactory::create();
    }

    public function mutatePageHTML(
        string $pageHTML
        ): string
    {
        $this->PreProcessedHTML = $pageHTML;
        $this->ClassParser->setHTMLSource($pageHTML);
        $this->runExtension();
        return $this->getProcessedPageHTML();
    }

    public function mutatePageCSS(
        string $pageCSS
        ): string
    {
        $mutatedCSS = $pageCSS.$this->getProcessedPageCSS();
        return str_replace(["\r","\n","    ","\t"],"", $mutatedCSS);
    }

    public function mutatePageJS(
        string $pageJS
    ):string {
        return $pageJS;
    }

    public function onCreateComponentContent(ComponentModel $componentModel, string $content):string {
        return $content;
    }

    public function onCreateComponentCSS(ComponentModel $componentModel, string $css): string {
        return $css;
    }

    public function onCreateComponentJS(ComponentModel $componentModel, string $js): string {
        return $js;
    }

    public function runExtension()
    {
        $this->registerClasses();
        $this->compileGroupedUtilityClasses();
        $this->compileUtilityClasses();
        $this->compileCustomUtilityClasses();
        $this->compileMediaQueryBreakpoints();
        $this->generatePostProcessHTML();
    }

    private function registerClasses()
    {
        foreach ($this->ClassParser->parse() as $classStatememt => $classParsingData) {
            $this->ClassRegistry[$classStatememt] = $classParsingData;
        }
        return $this;
    }

    private function compileGroupedUtilityClasses()
    {
        foreach ($this->GroupedAssetsManager->compileAssets() as $groupName => $members) {

            foreach ($this->ClassRegistry as $classStatement => $classDetails) {
                if  (in_array($groupName,$classDetails['classList'])) {
                    $updatedClassList = [];
                    foreach ($classDetails['minifiedClassNames'] as $unExtractedClassName) {
                        if ($unExtractedClassName===$groupName) {
                            foreach ($members as $member) {
                                array_push($updatedClassList,$member);
                            }
                        } else {
                            array_push($updatedClassList,$unExtractedClassName);
                        }
                    }
                    $this->ClassRegistry[$classStatement]['minifiedClassNames'] = $updatedClassList;
                    $this->ClassRegistry[$classStatement]['classList'] = $updatedClassList;
                }

            }
        }
    }

    private function compileUtilityClasses()
    {
        foreach ($this->AssetsManager->compileAssets() as $selector => $rules) {

            $this->ListOfUtilityClasses[$selector] = $rules;

            $minifiedClassName = $this->ClassNameMinifier->create();

            foreach ($this->ClassRegistry as $classStatement => $classDetails) {
                
                if (in_array($selector,$classDetails['classList'])) {
                    
                    $newArr = [];
                    foreach ($classDetails['minifiedClassNames'] as $unMinifiedClassName) {
                        if ($unMinifiedClassName===$selector) {
                            array_push($newArr,$minifiedClassName);
                        } else {
                            array_push($newArr,$unMinifiedClassName);
                        }
                    }
                    $this->ClassRegistry[$classStatement]['minifiedClassNames'] = $newArr;
                    $this->UsedUtilityClasses[$minifiedClassName] = $rules;
                }
            }
        }
 
    }

    private function compileCustomUtilityClasses() 
    {
        foreach ($this->ThemeCustomAssetsManager->loadCustomAssets() as $selector => $options) {
            $rules = $options['rules'];
            $this->ListOfUtilityClasses[$selector] = $rules;
            $minifiedClassName = $this->ClassNameMinifier->create();
            foreach ($this->ClassRegistry as $classStatement => $classDetails) {
                if (in_array($selector,$classDetails['classList'])) {
                    $newArr = [];
                    foreach ($classDetails['minifiedClassNames'] as $unMinifiedClassName) {
                        if ($unMinifiedClassName===$selector) {
                            array_push($newArr,$minifiedClassName);
                        } else {
                            array_push($newArr,$unMinifiedClassName);
                        }
                    }
                    $this->ClassRegistry[$classStatement]['minifiedClassNames'] = $newArr;
                    $this->UsedUtilityClasses[$minifiedClassName] = $rules;
                }
            }
        }
    }

    private function compileMediaQueryBreakpoints()
    {
        foreach ($this->MediaQueryManager->compileAssets() as $widthQueryClause => $mediaQueryAsset) {
            foreach ($mediaQueryAsset['selector_list'] as $selectorName => $selectorValue) {
                $minifiedClassName = $this->ClassNameMinifier->create();
                foreach ($this->ClassRegistry as $classStatement => $classDetails) {
                    if (in_array($selectorName,$classDetails['classList'])) {
                        $newArr = [];
                        foreach ($classDetails['minifiedClassNames'] as $unMinifiedClassName) {
                            if ($unMinifiedClassName===$selectorName) {
                                array_push($newArr,$minifiedClassName);
                            } else {
                                array_push($newArr,$unMinifiedClassName);
                            }
                        }
                        $this->ClassRegistry[$classStatement]['minifiedClassNames'] = $newArr;
                        if (!isset($this->UsedMediaQueryBreakpoints[$widthQueryClause])) {
                            $this->UsedMediaQueryBreakpoints[$widthQueryClause] = [];
                        }
                        $this->UsedMediaQueryBreakpoints[$widthQueryClause][$minifiedClassName] = $selectorValue;
                    }
                }
            }
        }
    }

    public function getProcessedPageCSS(): string
    {
        return $this->stringifyAllUsedCSS();
    }

    private function stringifyAllUsedCSS()
    {
        $css = '';
        foreach ($this->UsedUtilityClasses as $selector => $rules) {
            $css .= '.'.$selector.'{'.$rules.'}';
        }
        foreach ($this->UsedMediaQueryBreakpoints as $breakpoint => $listOfRules) {
            $css .= '@media screen and ('.$breakpoint.'px){';
                foreach ($listOfRules as $selector => $rules) {
                    $css .= '.'.$selector.'{'.$rules.'}';
                }
            $css .= '}';
        }
        $this->UsedUtilityClasses = [];
        $this->UsedMediaQueryBreakpoints = [];
        $this->ClassRegistry = [];
        return $css;
    }

    private function generatePostProcessHTML()
    {

        $this->PostProcessedHTML = $this->PreProcessedHTML;

        foreach ($this->ClassRegistry as $classStatement => $classDetails) {

            $classAttributeMinified = 'class="'.implode(' ',$classDetails['minifiedClassNames']).'"';

            $this->PostProcessedHTML = str_replace($classStatement,$classAttributeMinified,$this->PostProcessedHTML);

        }
    }

    public function getProcessedPageHTML(): string
    {
        return $this->PostProcessedHTML;
    }

    public function getDashboard()
    {
        ob_start();
        include __dir__.'/Dashboard/dashboard.php';
        $dashboard = ob_get_contents();
        ob_end_clean();
        return $dashboard;
    }

    public function setRawPageCSS(string $rawPageCSS) {
        return '';
    }

}
