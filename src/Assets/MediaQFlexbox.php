<?php

namespace Kenjiefx\VentaCSS\Assets;

use Kenjiefx\VentaCSS\Services\VentaDashboard;
use Kenjiefx\VentaCSS\Services\MediaQueryManager;

class MediaQFlexbox
{
    public static function assign(
        MediaQueryManager $MediaQueryManager,
        VentaDashboard $VentaDashboard
        )
    {
        foreach ($MediaQueryManager->getRegistry() as $widthQueryClause => $registryItem) {
            $MediaQueryManager->addItemToSelectorListRegistry(
                $widthQueryClause, 'display-flex-'.$registryItem['selector_clause'], 'display:flex;'
            );
        }
        foreach ($MediaQueryManager->getRegistry() as $widthQueryClause => $registryItem) {
            $MediaQueryManager->addItemToSelectorListRegistry(
                $widthQueryClause, 'flex-direction-column-'.$registryItem['selector_clause'], 'flex-direction:column;'
            );
        }
        foreach ($MediaQueryManager->getRegistry() as $widthQueryClause => $registryItem) {
            $MediaQueryManager->addItemToSelectorListRegistry(
                $widthQueryClause, 'flex-direction-row-'.$registryItem['selector_clause'], 'flex-direction:row;'
            );
        }
    }
}
