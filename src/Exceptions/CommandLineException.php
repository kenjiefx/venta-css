<?php

namespace Kenjiefx\VentaCss\Exceptions;
use \Kenjiefx\VentaCss\Cli\Console;


class CommandLineException extends \Exception {

    public static function incomplete(
        string $message
        )
    {
        Console::out("Error: ${message}",TOF_ERROR);
        exit();
    }

}
