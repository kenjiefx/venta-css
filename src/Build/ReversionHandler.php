<?php

namespace Kenjiefx\VentaCss\Build;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Build\HTML\FileSys;
use \Kenjiefx\VentaCss\Venta\Venta;

class ReversionHandler {

    private array $argv;
    private Venta $venta;
    private string $namespace;

    public function __construct(
        array $argv
    )
    {
        try {
            if (!isset($argv[2])) {
                throw new \Exception(
                    'Revert command requires directory'
                );
            }
        } catch (\Exception $e) {
            CoutStreamer::cout("Error {$e->getMessage()}",'error');
            exit();
        }
        $this->argv = $argv;
        $this->namespace = $argv[2];
        $this->venta = new Venta($this->namespace);
    }

        public function pull(
            $dirName = null
            )
        {
            $dir = $dirName ?? '/';
            try {
                if (!file_exists($this->venta->getFrontend().$dir)) {
                    throw new \Exception('Build directory /'.$this->namespace.' not found');
                }
            } catch (\Exception $e) {
                CoutStreamer::cout('Error: '.$e->getMessage(),'error');
                exit();
            }

            $pullDir = $this->venta->getBackend().$dir;

            FileSys::clear($pullDir);
            
            FileSys::traverse($this->venta->getFrontend().$dir,function(
                $filePath,
                $fileName,
                $fileExtension,
                $closureArgs
            ){
                if ($fileExtension==='dir') {
                    $puller = new ReversionHandler($this->argv);
                    $puller->pull('/'.$fileName);
                    return;
                }
                copy($filePath,$closureArgs['pullDir'].'/'.$fileName);
            },['pullDir'=>$pullDir]);
        }



}
