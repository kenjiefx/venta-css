<?php

namespace Kenjiefx\VentaCss\Logger;
use \Kenjiefx\VentaCss\Cli\Console;

#[\Attribute]
class ConfigActionLogs {

    public function __construct(
        string $message
        )
    {
        echo $message;
    }



}
