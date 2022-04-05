<?php

namespace Kenjiefx\VentaCss\Build\CSS;

class CSSChunker {

    private $raw;
    private $nativeBlocks;
    private $mediaBlocks;


    public function __construct($raw)
    {
        $this->raw = $raw;
        $this->nativeBlocks = '';
        $this->mediaBlocks = [];
    }

    public function init()
    {
        $isPrunable = true;
        $i = 0;
        while ($isPrunable) {
            $isPrunable = $this->prune();
            $i++;
        }
        return $this;
    }

    public function getNativeBlocks()
    {
        return $this->nativeBlocks;
    }

    public function getMediaBlocks()
    {
        return $this->mediaBlocks;
    }

    private function prune()
    {
        $raw = $this->raw;
        $starter = strpos($raw,'{');
        if (!$starter)
            return false;

        // echo "-----".PHP_EOL;
        // echo $raw.PHP_EOL;
        $prulen = $this->getPrulen();
        // var_dump($prulen);

        if ($prulen) {
            $selector = substr($raw,0,$prulen);
        } else {
            $selector = $raw;
        }

        if (str_contains($selector,'@media')) {
            # Getting extra selector content;
            $hasExtra = true;
            while ($hasExtra) {
                $closer = strpos($this->raw,'}');
                $extra = substr($this->raw,0,$closer+1);
                $selector = $selector.' '.$extra;
                $this->getPrulen();
                if ($this->raw[0]==='}'||$this->raw[0]==='@') {
                    $hasExtra = false;
                }
            }

            if ($this->raw[0]==='}') {
                $this->raw = ltrim($this->raw, '}');
            }

            array_push($this->mediaBlocks,$selector.'}');

        } else {
            // echo $selector.PHP_EOL;
            $this->nativeBlocks .= $selector.' ';
        }

        // echo "-----".PHP_EOL;

        if (!$prulen)
            return false;

        return true;
    }

    private function getPrulen()
    {
        $raw = $this->raw;
        $closer = strpos($raw,'}');
        if (!$closer)
            return false;
        $prulen = ($closer-strlen($raw)+1);
        $this->raw = trim(substr($raw,$prulen,strlen($raw)));
        return $prulen;
    }

    public static function getMediaBlockContent(
        string $raw
        )
    {
        $starter = strpos($raw,'{');
        $closer = strrpos($raw,'}');
        $raw = substr($raw,$starter,$closer-strlen($raw));
        $starter = strpos($raw,'{');
        return substr($raw,$starter+1);
    }

    public static function getMediaBlockStatement(
        string $raw
        )
    {
        $starter = strpos($raw,'{');
        return substr($raw,0,$starter-strlen($raw));
    }

}
