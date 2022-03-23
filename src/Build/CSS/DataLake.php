<?php

namespace Kenjiefx\VentaCss\Build\CSS;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;

class DataLake {

    private array $lake;

    public function __construct()
    {
        $this->lake = [];
    }

    public function feed(
        string $key,
        array $value
        )
    {
        $this->lake[$key] = $value;
    }

    public function inject(
        string $key,
        array $value
        )
    {
        $this->lake[$key][$value[0]] = $value[1];
    }

    public function sort()
    {
        asort($this->lake);
    }

    public function fetch()
    {
        return $this->lake;
    }



}
