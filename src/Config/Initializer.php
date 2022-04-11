<?php

namespace Kenjiefx\VentaCss\Config;
use \Kenjiefx\VentaCss\Cli\Console;
use \Kenjiefx\VentaCss\Build\ReversionHandler;
use \Kenjiefx\VentaCss\Exceptions\ConfigExceptions;

class Initializer {

    private const CONFIG_PATH = '/venta.config.json';

    /**
     * @method hook
     * Starts a new Venta instance
     *
     * NOTE: Config file must be completely deleted
     * when we want to start over. This method
     * does not override existing config file
     */
    public static function hook (
        string $dir
        )
    {
        $path = ROOT.Self::CONFIG_PATH;
        if (!file_exists($path))
            return Self::createApp($dir);
        Console::out(
            'Warning: venta.config.json already exists. No new instance is created.',
            TOF_WARNING
        );
    }

    /**
     * Creates a new instance of Venta app
     * in your working directory
     *
     * @param string $dir
     * The directory where build would be taking
     * place. This is your public-facing directory.
     */
    private static function createApp (
        string $dir
        )
    {
        Initializer::vntDir();
        Initializer::namespace($dir);
        Initializer::config($dir);
    }

    /**
     * @method vntDir
     * Creates a /vnt directory where we save
     * a copy of the build directory. This is
     * also where we extract the original files
     * when doing a revert action
     *
     */
    private static function vntDir()
    {
        $vntDir = ROOT.'/vnt';
        if (!is_dir($vntDir))
            return mkdir($vntDir);
    }

    /**
     * @method namespace
     * A directory inside the /vnt directory
     *
     * @param string $dir
     * The directory where build would be taking
     * place. This is your public-facing directory.
     */
    private static function namespace(
        string $dir
        )
    {
        $nmspcPath = ROOT."/vnt/{$dir}";
        if (!is_dir($nmspcPath))
            return mkdir($nmspcPath);
    }

    /**
     * @method config
     * Creates venta.config.json where we save all our
     * app configuration
     *
     * @param string $dir
     * The directory where build would be taking
     * place. This is your public-facing directory.
     *
     */
    private static function config(
        string $dir
        )
    {
        $config = [
            'namespace' =>$dir,
            'ignore'    =>[],
            'extensions'=>[]
        ];
        $configPath = ROOT.'/venta.config.json';
        if (!file_exists($configPath))
            file_put_contents($configPath,json_encode($config));
    }

    public static function load()
    {
        $path = ROOT.Self::CONFIG_PATH;
        try {
            if (!file_exists($path)) {
                throw new ConfigExceptions();
            }
        } catch (\Exception $e) {
            ConfigExceptions::notFound();
        }

        try {
            $config = json_decode(
                file_get_contents($path),
                TRUE
            );
            if (json_last_error()!==JSON_ERROR_NONE) {
                throw new ConfigExceptions(
                    'Incorrect JSON Format'
                );
            }
            if (!isset($config['namespace'])||trim($config['namespace'])==='') {
                throw new ConfigExceptions(
                    'Requires namespace'
                );
            }
        } catch (\Exception $e) {
            ConfigExceptions::invalid($e->getMessage());
        }

        return $config;
    }

}
