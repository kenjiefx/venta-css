<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS\Assets;
use Kenjiefx\VentaCSS\Services\AssetsManager;
use Kenjiefx\VentaCSS\Services\VentaDashboard;

class Padding {

    public static function assign(
        AssetsManager $AssetsManager,
        VentaDashboard $VentaDashboard
        )
    {

        # The common group name where this selector belongs to
        $GROUP = 'Padding';

        # Explain what this selector is all about
        $DESCRIPTION = '';

        # The human-readable CSS Selector
        $SELECTOR = 'padding';

        # Getting the configuration for this selector
        $config = $AssetsManager->getRaw($SELECTOR);

        # The maximum value to be given for the largest slice value
        $max = intval($config['max']);

        # The minimul value to be given for the lowest slice value
        $min = intval($config['min']);

        # Whether it's a negatively or positively signed value
        $sign = $config['sign'] ?? '';

        # The number of variants to this selector
        $slice = intval($config['slice']);

        # Separator between the selector name and varaint value
        $delimiter = $config['delimiter'];

        # The difference of the values between the variants
        $increment = ($max - $min) / $slice;

        $directions = [
            '-x' => ['padding-left','padding-right'],
            '-x-' => ['padding-left','padding-right'],
            '-y' => ['padding-top','padding-bottom'],
            '-y-' => ['padding-top','padding-bottom'],
            '-left' => ['padding-left'],
            '-left-' => ['padding-left'],
            '-right' => ['padding-right'],
            '-right-' => ['padding-right'],
            '-top' => ['padding-top'],
            '-top-' => ['padding-top'],
            '-bottom' => ['padding-bottom'],
            '-bottom-' => ['padding-bottom']
        ];

        $k = 0;
        foreach ($directions as $directionKey => $directionValue) {

            $toIncrement = $min;
            $i = 0;

            $selectorSign = (($k+1)%2===0) ? '-' : '';

            while($i<$slice){

                # Generating the actual selector name
                $selector = $SELECTOR.$directionKey.$delimiter.($i+1);

                # Generating global selector name
                $selectorGlobal = $SELECTOR.$directionKey;

                # CSS rules give to this selector
                $ruleStatement = '';

                foreach ($directionValue as $rule) {
                    $ruleStatement .= $rule.':'.$selectorSign.round($toIncrement+$increment,3).$config['unit'].';';
                }

                $AssetsManager->setRefined($selector,$ruleStatement);
                $VentaDashboard->addEntity($GROUP,$selectorGlobal,$DESCRIPTION,$selector,$ruleStatement);

                $toIncrement = $toIncrement+$increment;
                $i++;
            }

            $k++;

        }

    }

}
