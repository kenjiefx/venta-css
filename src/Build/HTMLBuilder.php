<?php

namespace Kenjiefx\VentaCss\Build;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Build\FileSys;

class HTMLBuilder {

  private string $namespace;
  private string $path;

  public function __construct(
    string $namespace
    )
  {
    $this->namespace = $namespace;
    $this->path = ROOT.'/'.$this->namespace;
  }

  public function build()
  {
    FileSys::traverse($this->path,function(
      $filePath,
      $fileName,
      $fileExtension,
      $closureArgs
    ){
      if ($fileName==='venta') {
        return;
      }
      if ($fileExtension==='html'||$fileExtension==='htm'||$fileExtension==='php') {
        $html = file_get_contents($filePath);
        $cssReference = json_decode(file_get_contents($closureArgs['vntDir'].'/venta/css.json'),TRUE);
        $regex = '/class="\s*(.*?)\s*"/s';
        preg_match_all($regex, $html, $matches, PREG_SET_ORDER, 0);
        foreach ($matches as $key => $value) {
          $classes = explode(' ',$value[1]);
          $aggregatedClasses = [];
          foreach ($classes as $class) {
            if (isset($cssReference[$class])) {
              $cssRefs = explode(' ',$cssReference[$class]);
              foreach ($cssRefs as $cssRef) {
                if (!in_array($cssRef,$aggregatedClasses)) {
                  array_push($aggregatedClasses,$cssRef);
                }
              }
            } else {
              if (!in_array($class,$aggregatedClasses)) {
                array_push($aggregatedClasses,$class);
              }
            }
          }
          $compressedClassNames = implode(' ',$aggregatedClasses);
          $parsed = str_replace($value[0],'class="'.$compressedClassNames.'"',$html);
          $html = $parsed;
        }
        file_put_contents($filePath,$html);
      }
    },['path'=>$this->path,'vntDir'=>ROOT.'/vnt/'.$this->namespace]);
  }

}
