<?php 

namespace Kenjiefx\VentaCSS\Integrations\Scratch;

use Kenjiefx\ScratchPHP\App\Interfaces\ConfigurationInterface;
use Kenjiefx\ScratchPHP\App\Interfaces\ThemeServiceInterface;
use Kenjiefx\ScratchPHP\App\Pages\PageModel;
use Kenjiefx\ScratchPHP\App\Themes\ThemeModel;
use Kenjiefx\VentaCSS\Options\OptionsCollector;
use Kenjiefx\VentaCSS\Services\BreakpointRegistrationService;
use Kenjiefx\VentaCSS\Services\ClassRegistrationService;

class BeforePageBuildService {

    public function __construct(
        public readonly OptionsCollector $optionsCollector,
        public readonly ClassRegistrationService $classRegistrationService,
        public readonly BreakpointRegistrationService $breakpointRegistrationService,
        public readonly ConfigurationInterface $configuration,
        public readonly ThemeServiceInterface $themeService,
    ) {}

    public function run(
        PageModel $pageModel
    ){
        $ventaDir = $this->getVentaDirPath($pageModel->theme);
        $options = $this->optionsCollector->collect($ventaDir);
        foreach ($options as $option) {
            $this->classRegistrationService->fromOption($option);
            $this->breakpointRegistrationService->fromOption($option);
        }
    }

    private function getVentaDirPath(
        ThemeModel $themeModel
    ){
        $themeName = $this->configuration->getThemeName();
        $themeDir = $this->themeService->getThemeDir($themeModel);
        return "{$themeDir}/venta";
    }

}