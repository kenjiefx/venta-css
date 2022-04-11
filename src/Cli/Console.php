<?php

namespace Kenjiefx\VentaCss\Cli;

class Console {

    /**
     * @var const OUTF
     */
    private const OUTF = "\033[0m";

    public function __construct()
    {
        define('TOF_SUCCESS',"\033[92m");
        define('TOF_ERROR',"\033[91m");
        define('TOF_WARNING',"\033[93m");
    }

    /**
     * @method out
     * Prints a message in the console
     *
     * @param string $message
     * The message that we need to print out
     * @param string|null $type
     * The type of message (i.e:warning,error,success). When
     * NULL, we default to the default white color
     *
     */
    public static function out(
        string $message,
        string|null $type = null
        )
    {
        $type = $type ?? "";
        echo $type.$message.Self::OUTF.PHP_EOL;
    }


    public static function log(
        string $classRef,
        string $methodRef
        )
    {
        $Method = new \ReflectionMethod($classRef,$methodRef);
        return $Method->getAttributes()[0]->newInstance();
    }

}
