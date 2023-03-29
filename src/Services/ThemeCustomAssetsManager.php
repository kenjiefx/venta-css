<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS\Services;
use Kenjiefx\ScratchPHP\App\Configuration\AppSettings;
use Kenjiefx\ScratchPHP\App\Themes\ThemeController;


class ThemeCustomAssetsManager {

    private string $themeName;

    private const UTILS_PATH = '/venta/css/venta.utils.json';

    public function __construct(
        private AppSettings $AppSettings,
        private ThemeController $themeController
        )
    {
        $this->themeName = $AppSettings::getThemeName();
    }

    public function loadCustomAssets()
    {
        $utilsPath = $this->themeController->getThemePath().Self::UTILS_PATH;
        if (!file_exists($utilsPath)){
            return [];
        }
        return json_decode(file_get_contents($utilsPath),TRUE);
    }
    







}