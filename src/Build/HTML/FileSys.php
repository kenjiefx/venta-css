<?php

namespace Kenjiefx\VentaCss\Build\HTML;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;

class FileSys
{

    public static function traverse(
        string $dirPath,
        \Closure $closure,
        array $closureArgs = null
        )
    {
        try {
            if (!is_dir($dirPath)) {
                throw new \Exception(
                    'Unable to traverse through '.$dirPath.
                    ' Either the path is not a directory or non-existent'
                );

            }
        } catch (\Exception $e) {
            CoutStreamer::cout('FileSys::Exception: '.$e->getMessage(),'error');
            exit();
        }

        $dirs = scandir($dirPath);
        foreach ($dirs as $dir) {
            if ($dir==='.'||$dir==='..') continue;
            $fileName = [];
            if (is_dir($dirPath.'/'.$dir)) {
              $fileName = [$dir,'dir'];
            } else {
              $fileName = explode('.',$dir);
            }
            $closure(
                $dirPath.'/'.$dir,
                $dir,
                $fileName[1],
                $closureArgs
            );
        }
    }

    public static function clear(
        string $dirPath
        )
    {
        $dirs = scandir($dirPath);
        foreach ($dirs as $dir) {
            if ($dir==='.'||$dir==='..')continue;
                if (is_dir($dirPath.'/'.$dir)) {
                    Self::clear($dirPath.'/'.$dir);
                    return;
                }
            unlink($dirPath.'/'.$dir);
        }
    }
}
