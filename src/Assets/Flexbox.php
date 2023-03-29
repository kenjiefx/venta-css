<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS\Assets;
use Kenjiefx\VentaCSS\Services\AssetsManager;
use Kenjiefx\VentaCSS\Services\VentaDashboard;

class Flexbox {

    public static function assign(
        AssetsManager $AssetsManager,
        VentaDashboard $VentaDashboard
        )
    {

        # The common group name where this selector belongs to
        $GROUP = 'Flexbox';

        # Explain what this selector is all about
        $DESCRIPTION = '';

        # The human-readable CSS Selector
        $SELECTOR = 'flex';

        $selectorGroups = [
            'display-flex' => 'display:flex',
            'flex-direction-column' => 'flex-direction:column',
            'flex-direction-column-reverse' => 'flex-direction:column-reverse',
            'flex-direction-row' => 'flex-direction:row',
            'flex-direction-row-reverse' => 'flex-direction:row-reverse',
            'flex-wrap-wrap' => 'flex-wrap:wrap',
            'flex-wrap-nowrap' => 'flex-wrap:nowrap',
            'flex-grow-1' => 'flex-grow-1'
        ];

        foreach ($selectorGroups as $key => $ruleStatement) {
            $AssetsManager->setRefined($key,$ruleStatement.';');
            $VentaDashboard->addEntity($GROUP,'flexbox','',$key,$ruleStatement.';');
        }

        $props = ['stretch','center','flex-start','flex-end','baseline','initial','inherit'];

        foreach ($props as $prop) {

            $selector = 'align-items-'.$prop;
            $ruleStatement = 'align-items:'.$prop.';';
            $AssetsManager->setRefined($selector,$ruleStatement);
            $VentaDashboard->addEntity($GROUP,'align-items','',$selector,$ruleStatement);
            // code...
        }

        $props = ['flex-start','flex-end','center','space-between','space-around','space-evenly','initial','inherit'];

        foreach ($props as $prop) {

            $selector = 'justify-content-'.$prop;
            $ruleStatement = 'justify-content:'.$prop.';';
            $AssetsManager->setRefined($selector,$ruleStatement);
            $VentaDashboard->addEntity($GROUP,'justify-content','',$selector,$ruleStatement);
            // code...
        }



    }

}
