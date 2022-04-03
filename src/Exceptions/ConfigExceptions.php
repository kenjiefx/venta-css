<?php

namespace Kenjiefx\VentaCss\Exceptions;
use \Kenjiefx\VentaCss\Cli\Console;


class ConfigExceptions extends \Exception {

    public static function notFound()
    {
        Console::out(
            'Error: Unable to find venta.config.json.',
            TOF_ERROR
        );
        Console::out(
            'If you have not initiated Venta, use the <hook><{dirName}> command.'
        );
        exit();
    }

    public static function invalid(
        string $message
        )
    {
        Console::out(
            "Error: Invalid venta.config.json: ${message}",
            TOF_ERROR
        );
        exit();
    }

}
