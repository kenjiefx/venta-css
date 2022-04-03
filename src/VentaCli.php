<?php

namespace Kenjiefx\VentaCss;
use \Kenjiefx\VentaCss\Cli\Registry;
use \Kenjiefx\VentaCss\Cli\Console;
use \Kenjiefx\VentaCss\Build\BuilderFacade;
use \Kenjiefx\VentaCss\Build\ReversionHandler;
use \Kenjiefx\VentaCss\Config\ConfigFacade;

class VentaCli extends Console {

    private array $argv;

    public function __construct(array $argv)
    {
        parent::__construct();
        $this->argv = $argv;
        $this->call();
    }

    public function call()
    {
        $command = $this->argv[1] ?? null;
        switch ($command) {
            case 'build':
                $builder = new BuilderFacade($this->argv);
                // $reversion = new ReversionHandler($this->argv);
                // $reversion->pull();
                $builder->build();
                break;
            case 'hook':
                $config = new ConfigFacade($this->argv);
                $config->create();
                $reversion = new ReversionHandler($this->argv);
                $reversion->pull();
                CoutStreamer::cout('VentaCSS App build directory created!','success');
                break;
            case 'revert':
                $reversion = new ReversionHandler($this->argv);
                $reversion->push();
                CoutStreamer::cout('Reverted successfully!','success');
                break;
            case '--v':
                CoutStreamer::cout('VentaCSS Version 1.0.0');
                break;
            default:
                Console::out(
                    'Command not found. Need help?'
                );
                break;
        }
    }


}
