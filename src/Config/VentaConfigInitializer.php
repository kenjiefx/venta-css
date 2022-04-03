<?php

namespace Kenjiefx\VentaCss\Config;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Build\ReversionHandler;
use \Kenjiefx\VentaCss\Exceptions\ConfigExceptions;

class VentaConfigInitializer {

    const CONFIG_PATH = '/venta.config.json';

    public static function hook (
        string $dir
        )
    {
        $path = ROOT.Self::CONFIG_PATH;
        if (!file_exists($path))
            Self::createApp($dir);

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

    private static function createApp (
        string $dir
        )
    {
        $vntDir = ROOT.'/vnt';
        $configPath = ROOT.'/venta.config.json';
        if (!file_exists($vntDir))
            mkdir($vntDir);
        $dirDir = $vntDir.'/'.$dir;
        if (!file_exists($dirDir))
            mkdir($dirDir);
        $config = [
            'namespace'=>$dir,
            'ignore'=>[],
            'extensions'=>[]
        ];
        file_put_contents($configPath,json_encode($config));
    }

}
