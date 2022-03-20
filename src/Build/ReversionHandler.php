<?php

namespace Kenjiefx\VentaCss\Build;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Build\FileSys;

class ReversionHandler {

  private string $namespace;

  public function __construct(
    $namespace
    )
  {
    $this->namespace = $namespace;
  }

  public function push()
  {

    $vntDir = ROOT.'/vnt/'.$this->namespace;
    $pushDir = ROOT.'/'.$this->namespace;

    FileSys::traverse($vntDir,function(
      $filePath,
      $fileName,
      $fileExtension,
      $closureArgs
      ){
        if ($fileExtension==='venta') {
          copy($closureArgs['vntDir'].'/venta/app.css',$closureArgs['pushDir'].'/venta/app.css');
          return;
        }
        if ($fileExtension==='dir') {
          $puller = new ReversionHandler($this->namespace.'/'.$fileName);
          $puller->push();
          return;
        }
        copy($filePath,$closureArgs['pushDir'].'/'.$fileName);
    },['pushDir'=>$pushDir,'vntDir'=>$vntDir]);
  }

  public function pull()
  {
    $buildDir = ROOT.'/'.$this->namespace;
    try {
      if (!file_exists($buildDir)) {
        throw new \Exception('Build directory /'.$this->namespace.' not found', 1);
      }
    } catch (\Exception $e) {
      CoutStreamer::cout('Error: '.$e->getMessage(),'error');
      exit();
    }
    $pullDir = ROOT.'/vnt/'.$this->namespace;
    if (!file_exists($pullDir)) {
      mkdir($pullDir);
    }

    FileSys::clear($pullDir);

    FileSys::traverse($buildDir,function(
      $filePath,
      $fileName,
      $fileExtension,
      $closureArgs
      ){
        if ($fileExtension==='dir') {
          $puller = new ReversionHandler($this->namespace.'/'.$fileName);
          $puller->pull();
          return;
        }
        copy($filePath,$closureArgs['pullDir'].'/'.$fileName);
    },['pullDir'=>$pullDir]);
  }

}
