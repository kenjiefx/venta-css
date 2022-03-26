<?php

namespace Kenjiefx\VentaCss\Build\HTML;

class FileSys
{

    public static function traverse(
        string $dirPath,
        \Closure $closure,
        array $closureArgs = null
        )
    {
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
