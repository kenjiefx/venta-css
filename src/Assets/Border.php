<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS\Assets;
use Kenjiefx\VentaCSS\Services\AssetsManager;
use Kenjiefx\VentaCSS\Services\VentaDashboard;

class Border {

    public static function assign(
        AssetsManager $AssetsManager,
        VentaDashboard $VentaDashboard
        )
    {

        # The common group name where this selector belongs to
        $GROUP = 'Border';

        # Explain what this selector is all about
        $DESCRIPTION = '';

        # The human-readable CSS Selector
        $SELECTOR = 'border';

        $directions = [
            '' => [''],
            'x' => ['top','bottom'],
            'y' => ['left','right'],
            'left' => ['left'],
            'right' => ['right'],
            'top' => ['top'],
            'bottom' => ['bottom']
        ];

        $styles = [
            'solid' => ['solid'],
            'none' => ['none']
        ];


        foreach ($directions as $name => $direction) {
            if ($name!=='') $name = '-'.$name;
            foreach ($styles as $styleName => $style) {
                $selector = 'border'.$name.'-'.$styleName;
                $ruleStatement = '';
                foreach ($direction as $rule) {
                    if ($rule!=='') $rule = '-'.$rule;
                    $ruleStatement .= 'border'.$rule.'-style:'.$styleName.';';
                }
                $AssetsManager->setRefined($selector,$ruleStatement);
                $VentaDashboard->addEntity('Border','border-style','',$selector,$ruleStatement);
            }

            $k = 1;

            while ($k<4) {

                $selector = 'border-width'.$name.'-'.$k;

                $ruleStatement = '';

                foreach ($direction as $rule) {

                    if ($rule!=='') $rule = '-'.$rule;

                    $ruleStatement .= 'border'.$rule.'-width:'.$k.'px;';

                }

                $AssetsManager->setRefined($selector,$ruleStatement);

                $VentaDashboard->addEntity('Border','border-width','',$selector,$ruleStatement);

                $k++;

            }


            $colors = $AssetsManager->getRaw('colors');

            foreach ($colors as $color => $hexCode) {

                $selector = 'border-color'.$name.'-'.$color;

                $ruleStatement = '';

                foreach ($direction as $rule) {

                    if ($rule!=='') $rule = '-'.$rule;

                    $ruleStatement .= 'border'.$rule.'-color:'.$hexCode.';';

                }

                $AssetsManager->setRefined($selector,$ruleStatement);

                $VentaDashboard->addEntity('Border','border-color','',$selector,$ruleStatement);


            }

        }


        /**
         * Border Radius
         */

         # The maximum value to be given for the largest slice value
         $max = 2;

         # The minimul value to be given for the lowest slice value
         $min = 1.60;

         # The number of variants to this selector
         $slice = 24;

         # The difference of the values between the variants
         $increment = ($max - $min) / $slice;


         $j = 0;

         while($j<$slice){

             $selector = 'border-radius-'.($j+1);

             $ruleStatement = 'border-radius:'.round($min+$increment,3).'em;';

             $AssetsManager->setRefined($selector,$ruleStatement);
             $VentaDashboard->addEntity($GROUP,'border-radius',$DESCRIPTION,$selector,$ruleStatement);

             $min = $min+$increment;

             $j++;
         }

    }

}
