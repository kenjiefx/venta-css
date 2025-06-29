<?php 

namespace Kenjiefx\VentaCSS\Integrations\Scratch;

use Kenjiefx\ScratchPHP\App\Configurations\ConfigurationInterface;
use Kenjiefx\ScratchPHP\App\Pages\PageModel;
use Kenjiefx\ScratchPHP\App\Themes\ThemeFactory;
use Kenjiefx\ScratchPHP\App\Themes\ThemeService;
use Kenjiefx\VentaCSS\Options\OptionsCollector;
use Kenjiefx\VentaCSS\Services\BreakpointRegistrationService;
use Kenjiefx\VentaCSS\Services\ClassRegistrationService;

class BeforePageBuildService {

    public function __construct(
        public readonly OptionsCollector $optionsCollector,
        public readonly ClassRegistrationService $classRegistrationService,
        public readonly BreakpointRegistrationService $breakpointRegistrationService,
        public readonly ConfigurationInterface $configuration,
        public readonly ThemeService $themeService,
        public readonly ThemeFactory $themeFactory
    ) {}

    public function run(
        PageModel $pageModel
    ){
        $ventaDir = $this->getVentaDirPath();
        $options = $this->optionsCollector->collect($ventaDir);
        foreach ($options as $option) {
            $this->classRegistrationService->fromOption($option);
            $this->breakpointRegistrationService->fromOption($option);
        }
    }

    private function getVentaDirPath(){
        $themeName = $this->configuration->getThemeName();
        $themeModel = $this->themeFactory->create($themeName);
        $themeDir = $this->themeService->getThemeDir($themeModel);
        return "{$themeDir}/venta";
    }

}