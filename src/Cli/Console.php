<?php

namespace Kenjiefx\VentaCss\Cli;

class Console {

    private const OUTF = "\033[0m";

    public function __construct()
    {
        define('TOF_SUCCESS',"\033[92m");
        define('TOF_ERROR',"\033[91m");
        define('TOF_WARNING',"\033[93m");
    }

    public static function out(
        string $message,
        string|null $type = null
        )
    {
        $type = $type ?? "";
        echo $type.$message.Self::OUTF.PHP_EOL;
    }

}
