<?php

namespace Kenjiefx\VentaCss\Build;
use \Kenjiefx\VentaCss\Cli\Console;
use \Kenjiefx\VentaCss\Build\HTML\FileSys;
use \Kenjiefx\VentaCss\Venta\Venta;
use \Kenjiefx\VentaCss\Logger\RevertActionLogs;

class ReversionManager {

    /**
     * @var array
     * Command line inputs
     */
    private array $argv;

    /**
     * @var Venta
     * Venta object contains useful APIs to work with
     * both the public-facing public directory (namespace)
     * and the backend copy saved under the /vnt directory
     */
    private Venta $venta;

    public function __construct(
        array $argv
    )
    {
        $this->argv = $argv;
        $this->venta = new Venta();
    }

    /**
     * @method pull
     * Pull means copying files from any sub-directories in the
     * public-facing directory to the backend copy saved under
     * the /vnt directory
     *
     * @param string|null $dirName
     * A path of a certain directory
     *
     */
    public function pull(
        $dirName = null
        )
    {
        $dir = $dirName ?? '/';
        $pullDir = $this->venta->getBackend().$dir;

        FileSys::clear($pullDir);

        FileSys::traverse($this->venta->getFrontend().$dir,function(
            $filePath,
            $fileName,
            $fileExtension,
            $closureArgs
        ){
            if ($fileExtension==='dir') {
                $puller = new ReversionManager ($this->argv);
                $puller->pull('/'.$fileName);
                return;
            }
            if (!file_exists($closureArgs['pullDir'].'/')) {
                mkdir($closureArgs['pullDir'].'/');
            }

            copy($filePath,$closureArgs['pullDir'].'/'.$fileName);

        },['pullDir'=>$pullDir]);

    }

    public function push(
        $dirName = null
        )
    {
        $dir = $dirName ?? '/';

        FileSys::traverse($this->venta->getBackend().$dir,function(
            $filePath,
            $fileName,
            $fileExtension,
            $closureArgs
        ){

            if ($fileName==='__venta.css.json') return;
            if ($fileName==='__venta.map.json') return;
            if ($fileName==='__venta.compiled.json') return;
            if ($fileName==='__venta.registry.json') return;
            if ($fileName==='__venta.usables.json') return;
            if ($fileExtension==='dir') {
                $puller = new ReversionManager($this->argv);
                $puller->push('/'.$fileName);
                return;
            }
            copy($filePath,$closureArgs['frontEnd'].'/'.$fileName);

        },['frontEnd'=>$this->venta->getFrontend().$dir,'backEnd'=>$this->venta->getBackend().$dir]);

        # CoutStreamer::cout('Reverting build directory: '.$this->venta->getFrontEnd().$dir.'...');

    }



}
