<?php

namespace Kenjiefx\VentaCss\Config;
use \Kenjiefx\VentaCss\ManagerRegistry;
use \Kenjiefx\VentaCss\Config\VentaConfigInitializer;
use \Kenjiefx\VentaCss\Exceptions\CommandLineException;
use \Kenjiefx\VentaCss\Exceptions\MissingComponentException;
use \Kenjiefx\VentaCss\Cli\Console;
use \Kenjiefx\VentaCss\Logger\ConfigActionLogs;

#[ManagerRegistry('ConfigManager')]
class ConfigManager {

    /**
     * @var array
     * Command line inputs
     */
    private array $argv;

    /**
     * @var string|null
     * The directory that we need to hook
     * into the Venta app
     *
     * Can be NULL when no directory is passed
     * with the command
     */
    private string|null $hookDir;


    public function __construct(
        array $argv
        )
    {
        $this->argv = $argv;
        $this->hookDir = $argv[2] ?? NULL;
    }

    /**
     * @method ready
     * Checks whether the command is valid
     *
     * @throws CommandLineException
     */
    #[ConfigActionLogs('Initializing Config')]
    public function ready()
    {
        try {
            if (!$this->hookDir) {
                throw new CommandLineException(
                  "<hook> command requires directory argument \nTry venta hook <dirName>"
                );
            }
        } catch (\Exception $e) {
            CommandLineException::incomplete($e->getMessage());
        }
    }

    /**
     * @method verify
     * Verifies whether the hooked directory exists
     * and is a real directory
     *
     * @throws MissingComponentException
     */
    #[ConfigActionLogs('Verifying Config')]
    private function verify()
    {
        try {
            $realDir = ROOT."/{$this->hookDir}";
            if (!is_dir($realDir)) {
                throw new MissingComponentException();
            }
        } catch (\Exception $e) {
            MissingComponentException::error($realDir);
        }

        Initializer::hook($this->hookDir);
    }

}
