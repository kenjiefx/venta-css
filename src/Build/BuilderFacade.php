<?php

namespace Kenjiefx\VentaCss\Build;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Build\ReversionHandler;
use \Kenjiefx\VentaCss\Build\CSSBuilder;
use \Kenjiefx\VentaCss\Build\FileSys;
use \Kenjiefx\VentaCss\Build\BuildManager;

class BuilderFacade {

  private array $argv;
  private string $namespace;

  public function __construct(
    array $argv
    )
  {
    $this->argv = $argv;
  }


  public function build()
  {
      $manager = new BuildManager($this->argv);
      $manager->build();

  }

  public function newBuild()
  {
    try {
      if (!isset($this->argv[2])) {
        throw new \Exception('Requires build directory', 1);
      }
    } catch (\Exception $e) {
      CoutStreamer::cout('Error: '.$e->getMessage(),'error');
    }
    $this->namespace = $this->argv[2];
    $this->backup();
    $timeStart = microtime(true);
    $originalFileSize = FileSys::getSize(ROOT.'/'.$this->namespace.'/venta/app.css');
    CoutStreamer::cout('Compressing venta/app.css...');
    $cssBuilder = new CSSBuilder($this->namespace);
    $cssBuilder->compile();
    CoutStreamer::cout('Saving venta/app.css...');
    $cssBuilder->save();
    CoutStreamer::cout('Compressing class names...');
    $htmlBuilder = new HTMLBuilder($this->namespace);
    $htmlBuilder->build();
    CoutStreamer::cout('Successfully compressed files!','success');
    $newFileSize = FileSys::getSize(ROOT.'/'.$this->namespace.'/venta/app.css');
    CoutStreamer::cout('Total build time: '.(microtime(true)-$timeStart).' seconds');
    CoutStreamer::cout('CSS reduced size from '.$originalFileSize.' → '.$newFileSize);
  }

  private function backup()
  {
    $reversionHandler = new ReversionHandler($this->namespace);
    $reversionHandler->pull();
    CoutStreamer::cout('Successfully initialized /'.$this->namespace.' namespace','success');
  }



}
