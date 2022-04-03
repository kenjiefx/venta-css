<?php

namespace Kenjiefx\VentaCss\Venta;
use \Kenjiefx\VentaCss\Config\VentaConfigInitializer as Config;
use \Kenjiefx\VentaCss\Exceptions\MissingComponentException;


class Venta {

    public string $namespace;
    private string $frontend;
    private string $backend;
    private array $config;
    private array $extensions;

    public function __construct()
    {
        $this->config     = Config::load();
        $this->namespace  = $this->config['namespace'];
        $this->extensions = $this->config['extensions'] ?? [];
        $this->frontend   = ROOT.'/'.$this->namespace;
        $this->backend    = ROOT.'/vnt/'.$this->namespace;
        $this->validate();
    }

    private function validate()
    {
        try {
            if (!file_exists($this->frontend)) {
                throw new MissingComponentException(
                    $this->frontend
                );
            }
            if (!file_exists($this->backend)) {
                throw new MissingComponentException(
                    $this->backend
                );
            }
        } catch (\Exception $e) {
            MissingComponentException::error($e->getMessage());
        }
    }

    public function compileSources()
    {
        $extensions = $this->extensions;
        $css = '';
        array_push($extensions,'app.css');

        try {
            foreach ($extensions as $extension) {
                $cssPath = "{$this->frontend}/venta/{$extension}";
                if (!file_exists($cssPath)) {
                    throw new \Exception(
                        "Unable to load Venta raw CSS file: /venta/{$extension}"
                    );
                }
                $css .= file_get_contents($cssPath);
            }
        } catch (\Exception $e) {
            CoutStreamer::cout('Error: '.$e->getMessage(),'error');
            exit();
        }

        return $css;
    }

    public function getFrontend(): string
    {
        return $this->frontend;
    }

    public function getBackend(): string
    {
        return $this->backend;
    }

}
