<?php

namespace Kenjiefx\VentaCss\Exceptions;
use \Kenjiefx\VentaCss\Cli\Console;

class MissingComponentException extends \Exception {
    
    public static function error(
        string $dir
        )
    {
        Console::out(
            'Error: Unable to find Build Directory: ',
            TOF_ERROR
        );
        Console::out($dir,TOF_ERROR);
        exit();
    }

}
