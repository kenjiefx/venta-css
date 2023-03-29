<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS\Assets;
use Kenjiefx\VentaCSS\Services\AssetsManager;
use Kenjiefx\VentaCSS\Services\VentaDashboard;

class LetterSpacing {

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
        $SELECTOR = 'letter-spacing';

        # Getting the configuration for this selector
        $config = $AssetsManager->getRaw($SELECTOR);

        # The maximum value to be given for the largest slice value
        $max = floatval($config['max']);

        # The minimul value to be given for the lowest slice value
        $min = floatval($config['min']);

        # Whether it's a negatively or positively signed value
        $sign = $config['sign'] ?? '';

        # The number of variants to this selector
        $slice = intval($config['slice']);

        # Separator between the selector name and varaint value
        $delimiter = $config['delimiter'];

        # The difference of the values between the variants
        $increment = ($max - $min) / $slice;

        $valueSigns = ['','-'];

        foreach ($valueSigns as $valueSign) {

            $i = 0;

            $incrementor = $min;

            while($i<$slice){

                # Generating the actual selector name
                $selector = $SELECTOR.$delimiter.$valueSign.($i+1);

                # Generating global selector name
                $selectorGlobal = $SELECTOR.$delimiter.$valueSign;

                # CSS rules give to this selector
                $ruleStatement = '';

                foreach ($config['rules'] as $rule) {
                    $ruleStatement .= $rule.':'.$valueSign.round($incrementor+$increment,3).$config['unit'].';';
                }

                $AssetsManager->setRefined($selector,$ruleStatement);
                $VentaDashboard->addEntity($GROUP,$selectorGlobal,$DESCRIPTION,$selector,$ruleStatement);

                $incrementor = $incrementor+$increment;
                $i++;
            }


        }


    }

}
