<?php

namespace Kenjiefx\VentaCss\Logger;
use \Kenjiefx\VentaCss\Cli\Console;

#[\Attribute]
class RevertActionLogs {

    public function __construct(
        string $message
        )
    {
        Console::progress($message);
    }

}
