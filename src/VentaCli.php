<?php

namespace Kenjiefx\VentaCss;
use \Kenjiefx\VentaCss\Cli\Registry;
use \Kenjiefx\VentaCss\Cli\Console;
use \Kenjiefx\VentaCss\Build\BuilderFacade;
use \Kenjiefx\VentaCss\Build\ReversionManager;
use \Kenjiefx\VentaCss\Config\ConfigFacade;

class VentaCli extends Console {

    /**
     * @var array
     * Command line inputs
     */
    private array $argv;

    /**
     * @var float
     * Venta CSS Version
     */
    private const VERSION = '1.0.0';


    public function __construct(array $argv)
    {
        parent::__construct();
        $this->argv = $argv;
        $this->call();
    }

    /**
     * @method call
     * Registers and serves the command
     */
    public function call()
    {
        match ($this->argv[1] ?? null) {
            'hook'    => $this->hook(),
            'build'   => $this->build(),
            'pull'    => $this->pull(),
            'version' => $this->version(),
            default   => $this->default()
        };

        # echo $callable;
        // switch ($command) {
        //     case 'build':
        //         $builder = new BuilderFacade($this->argv);
        //         // $reversion = new ReversionHandler($this->argv);
        //         // $reversion->pull();
        //         $builder->build();
        //         break;
        //     case 'hook':
        //         $config = new ConfigFacade($this->argv);
        //         $config->create();
        //         $reversion = new ReversionHandler($this->argv);
        //         $reversion->pull();
        //         CoutStreamer::cout('VentaCSS App build directory created!','success');
        //         break;
        //     case 'revert':
        //         $reversion = new ReversionHandler($this->argv);
        //         $reversion->push();
        //         CoutStreamer::cout('Reverted successfully!','success');
        //         break;
        //     case '--v':
        //         Console::out('VentaCSS Version 1.0.0');
        //         break;
        //     default:
        //         Console::out('Command not found. Need help?');
        //         break;
        // }
    }

    /**
     * @method hook
     * Initializes the application and hooks
     * a build directory
     */
    private function hook()
    {
        //Console::out('Initializing config');
        (new ConfigFacade($this->argv))->ready();
        //(new ConfigManager($this->argv))->ready();
        //$this->pull();
        //Console::out('Venta App is ready! Run build command using <venta build>');
    }


    private function push()
    {
        (new ReversionManager($this->argv))->push();
    }

    /**
     * @method pull
     * Pull means copying files from any sub-directories in the
     * public-facing directory to the backend copy saved under
     * the /vnt directory
     */
    private function pull()
    {
        Console::out(
          'Pulling in build directory...'
        );

        (new ReversionManager($this->argv))->pull();

        Console::out(
          'Build directory pulled in successfully!'
          ,TOF_SUCCESS
        );

        return $this;
    }

    private function revert()
    {

    }

    private function build()
    {
        (new ReversionManager($this->argv))->pull();
    }

    /**
     * @method version
     * Displays the current Venta CSS Version
     */
    private function version()
    {
        Console::out("Venta CSS Version ".VentaCli::VERSION,TOF_SUCCESS);
    }

    /**
     * @method default
     * Routes all command that aren't listed here
     */
    private function default()
    {
        Console::out("Command not found. Need help? \nType <venta help>");
    }


}
