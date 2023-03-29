<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS\Assets;
use Kenjiefx\VentaCSS\Services\AssetsManager;
use Kenjiefx\VentaCSS\Services\VentaDashboard;

class FontWeight {

    public static function assign(
        AssetsManager $AssetsManager,
        VentaDashboard $VentaDashboard
        )
    {

        # The common group name where this selector belongs to
        $GROUP = 'Text';

        # Explain what this selector is all about
        $DESCRIPTION = '';

        # The human-readable CSS Selector
        $SELECTOR = 'font-weight';

        $rule = 'font-weight';


        $i = 0;


        while($i<10){

            # Generating the actual selector name
            $selector = $SELECTOR.'-'.($i+1);

            # CSS rules give to this selector
            $ruleStatement = '';

            if ($i<9) {
                $ruleStatement = $rule.':'.(($i+1)*100).';';
            } else {
                $ruleStatement = 'font-weight:bold;text-shadow: 1.5px 0 0 currentColor;';
            }

            $AssetsManager->setRefined($selector,$ruleStatement);
            $VentaDashboard->addEntity($GROUP,$SELECTOR,$DESCRIPTION,$selector,$ruleStatement);

            $i++;
        }

        $AssetsManager->setRefined('font-weight-bold','font-weight:bold;');
        $VentaDashboard->addEntity($GROUP,$SELECTOR,$DESCRIPTION,'font-weight-bold','font-weight:bold;');




    }

}
