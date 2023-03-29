<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS\Assets;
use Kenjiefx\VentaCSS\Services\AssetsManager;
use Kenjiefx\VentaCSS\Services\VentaDashboard;

class Color {

    public static function assign(
        AssetsManager $AssetsManager,
        VentaDashboard $VentaDashboard
        )
    {

        # The common group name where this selector belongs to
        $GROUP = 'Color';

        # Explain what this selector is all about
        $DESCRIPTION = '';

        # The human-readable CSS Selector
        $SELECTOR = 'color';

        $colors = $AssetsManager->getRaw('colors');

        foreach ($colors as $color => $hexCode) {

            $selector = 'color-'.$color;

            $ruleStatement = 'color:'.$hexCode.';';

            $AssetsManager->setRefined($selector,$ruleStatement);

            $VentaDashboard->addEntity('Color','color','',$selector,$ruleStatement);

        }

    }

}
