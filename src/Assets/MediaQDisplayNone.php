<?php

namespace Kenjiefx\VentaCSS\Assets;

use Kenjiefx\VentaCSS\Services\VentaDashboard;
use Kenjiefx\VentaCSS\Services\MediaQueryManager;

class MediaQDisplayNone
{
    public static function assign(
        MediaQueryManager $MediaQueryManager,
        VentaDashboard $VentaDashboard
        )
    {
        foreach ($MediaQueryManager->getRegistry() as $widthQueryClause => $registryItem) {
            $MediaQueryManager->addItemToSelectorListRegistry(
                $widthQueryClause, 'display-none-'.$registryItem['selector_clause'], 'display:none;'
            );
        }
    }
}
