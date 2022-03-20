<?php

namespace Kenjiefx\VentaCss\Build;

class FileSys {

  public static function traverse(
    string $dirPath,
    \Closure $closure,
    array $closureArgs = null
    )
  {
    $dirs = scandir($dirPath);
    foreach ($dirs as $dir) {
      if ($dir==='.'||$dir==='..'){continue;};
      $individualFilePath = $dirPath.'/'.$dir;
      $fileName = [];
      if (is_dir($individualFilePath)) {
        $fileName = [$dir,'dir'];
      } else {
        $fileName = explode('.',$dir);
      }

      $closure($individualFilePath,$dir,$fileName[1],$closureArgs);
    }
  }

  public static function clear(
    string $dirPath
    )
  {
    $dirs = scandir($dirPath);
    foreach ($dirs as $dir) {
      if ($dir==='.'||$dir==='..'){continue;};
      if (is_dir($dirPath.'/'.$dir)) {
        Self::clear($dirPath.'/'.$dir);
        return;
      }
      unlink($dirPath.'/'.$dir);
    }
  }

  /**
  * Converts bytes into human readable file size.
  *
  * @author Mogilev Arseny
  */
  public static function getSize(
    string $path
    )
  {
    $fileSize = filesize($path);
    $bytes = floatval($fileSize);
    $arBytes = array(
      0 => array(
          "UNIT" => "TB",
          "VALUE" => pow(1024, 4)
      ),
      1 => array(
          "UNIT" => "GB",
          "VALUE" => pow(1024, 3)
      ),
      2 => array(
          "UNIT" => "MB",
          "VALUE" => pow(1024, 2)
      ),
      3 => array(
          "UNIT" => "KB",
          "VALUE" => 1024
      ),
      4 => array(
          "UNIT" => "B",
          "VALUE" => 1
      ),
    );
    foreach($arBytes as $arItem)
    {
        if($bytes >= $arItem["VALUE"])
        {
            $result = $bytes / $arItem["VALUE"];
            $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
            break;
        }
    }
    return $result;
  }

}
