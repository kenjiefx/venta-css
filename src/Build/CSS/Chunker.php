<?php

class Chunker {

    /**
     * @var $StyleSheet
     * An object containing usable methods
     * for parsing a CSS Stylesheet
     */
    private StyleSheet $sheet;

    /**
     * @var $nativeChunk
     * A string of CSS that aren't part of
     * any media query blocks
     */
    private string $nativeChunk = '';

    /**
     * @var $mediaQChunk
     * An array of media query blocks
     * parsed from the raw stylesheet
     */
    private array $mediaQChunks = [];

    /**
     * @var $commentChunks
     * An array of comment blocks parse
     * parsed from the raw stylesheet
     */
    private array $commentChunks = [];


    public function __construct(
        StyleSheet $StyleSheet
        )
    {
        $this->sheet = $StyleSheet;
    }

    /**
     * @method init
     * Initiates the parsing of the raw
     * stylesheet. Return itself so that
     * we can call the get chunker methods.
     *
     * @return object Chunker
     */
    public function init()
    {
        $cleared = false;
        $i = 0;
        while (!$cleared) {
            $cleared = $this->prune();
            $i++;
        }
        return $this;
    }

    /**
     * @method isCleared
     * Checks whether there are still contents
     * that needs to be parsed
     *
     * @return bool cleared or not
     */
    private function isCleared()
    {
        return is_null($this->sheet->charPos('{'));
    }

    /**
     * @method prune
     * Pruning works by first, trimming leading whitespaces,
     * identifying whether the next block is native, a comment,
     * or a media query bock
     *
     * For this version, we only allow class selectors, hence
     * the raw sheet is filtered.
     *
     * @return bool
     */
    private function prune()
    {
        $this->sheet->snip()->filter();
        $this->identify();
        return $this->isCleared();
    }

    /**
     * @method identify
     * Identify whether the next block is a native, a comment,
     * or a media query block
     *
     * @return object Chunker
     */
    private function identify()
    {
        $fChar = $this->sheet->fChar();
        switch ($fChar) {
            case '/':
                $this->pushAsComment();
                break;
            case '.':
                $this->pushAsNative();
                break;
            case '@':
                $this->pushAsMediaQuery();
                break;
            default:
                break;
        }
        return $this;
    }

    /**
     * @method pushAsComment
     * Pushes a new comment block to the $commentChunks array
     *
     * @throws InvalidCSSSyntaxException
     */
    private function pushAsComment()
    {
        try {
            $charPos = $this->sheet->charPos('*');
            if ($charPos!==1) {
                throw new \Exception(
                    'Invalid CSS Comment Syntax'
                );
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit();
        }
        $cdn = $this->sheet->locate('/');
        array_push (
            $this->commentChunks,
            $this->sheet->crop($cdn)
        );
        $this->sheet->cut($cdn);
    }

    /**
     * @method pushAsNative
     * Appends a new selector block in a form of string
     * to the $nativeChunk property
     *
     * @throws UnclosedSelectorException
     */
    private function pushAsNative()
    {
        try {
            $cdn = $this->sheet->locate('}');
            if (empty($cdn)) {
                throw new \Exception(
                  'Unclosed CSS'
                );
            }
        } catch (\Exception $e) {
            exit();
        }
        $cmp = [0,$cdn[0]];
        $this->nativeChunk .= $this->sheet->crop($cmp).' ';
        $this->sheet->cut($cmp);
    }

    /**
     * @method pushAsMediaQuery
     * Pushes a new media query block to the $commentChunks array
     */
    private function pushAsMediaQuery()
    {
        $cdn = [0,$this->sheet->clsPos()];
        array_push (
            $this->mediaQChunks,
            $this->sheet->crop($cdn)
        );
        $this->sheet->cut($cdn);
    }

    public function test()
    {
        echo $this->nativeChunk;
        # var_dump($this->mediaQChunks);
        # echo $this->sheet->get();
    }

}
