<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS\Factories;
use Kenjiefx\VentaCSS\VentaConfig;

class VentaConfigFactory {

    private static $instance;

    public static function create()
    {
        if (!isset(static::$instance)) {
            static::$instance = new VentaConfig;
        }
        return static::$instance;
    }

}
