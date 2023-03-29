<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS\Services;
use Kenjiefx\ScratchPHP\App\Themes\ThemeController;


class GroupedAssetsManager {

    private static array $groups;

    private const GROUPED_CSS_JSON_DIR = '/venta/css';

    public function __construct(
        private ThemeController $themeController
    ){
        
    }

    public function compileAssets()
    {
        if (!isset(static::$groups)) {
            static::$groups = [];
            $widgetCssJsonDirPath = $this->themeController->getThemePath().Self::GROUPED_CSS_JSON_DIR;
            if (is_dir($widgetCssJsonDirPath)) {
                foreach (scandir($widgetCssJsonDirPath) as $widgetCssJsonFile) {
                    if ($widgetCssJsonFile==='.'||$widgetCssJsonFile==='..') continue;
                    $fileNameTokens = explode('.',$widgetCssJsonFile);
                    $last = count($fileNameTokens) - 1;
                    if ($fileNameTokens[$last]!=='json') continue;
                    $widgetCssJsonPath = $widgetCssJsonDirPath.'/'.$widgetCssJsonFile;
                    $this->parseGroups(
                        $this->loadCssJsonFile($widgetCssJsonPath)
                    );
                }
            }
        }
        
        return static::$groups;
    }

    private function parseGroups(
        array $groupedSelectors
        )
    {
        foreach ($groupedSelectors as $groupedSelector) {
            foreach ($groupedSelector as $groupedSelectorName => $groupedSelectorMembers) {
                static::$groups[$groupedSelectorName] = explode(' ',$groupedSelectorMembers);
            }
        }
    }

    private function loadCssJsonFile(
        string $filePath
        )
    {
        if (!file_exists($filePath)) return [];
        return json_decode(
            file_get_contents($filePath),TRUE
        );
    }

}
