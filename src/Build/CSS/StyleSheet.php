<?php

class StyleSheet {

    /**
     * @var $rawSheet
     * A string of CSS, that is usually from a combined
     * CSS files in the /venta directory
     */
    private string $rawSheet;


    public function __construct(
        string $rawSheet
        )
    {
        $this->rawSheet = $rawSheet;
    }

    /**
    * @method snip
     * Removes whitespaces in the Stylesheet
     * This is to make sure that we correctly
     * identify the first character and further
     * tell the type of Selector we parse next
     *
     * @return object $this
     */
    public function snip()
    {
        $this->rawSheet = trim($this->rawSheet);
        return $this;
    }

    /**
     * @method fChar
     * Gets the first character in the StyleSheet.
     * This helps us indentify what kind of Selector
     * do we need to parse next
     *
     * @return string
     */
    public function fChar()
    {
        return $this->rawSheet[0];
    }

    /**
     * @method locate
     * Locates all the position of a certain
     * character across the stylesheet
     *
     * @param string $char
     * The character that we need to get the
     * position of
     *
     * @return array $pos
     * All the positions of the character in the
     * CSS Stylesheet
     */
    public function locate(
        string $char
        )
    {
        $pos = [];
        $i = 0;
        foreach (str_split($this->rawSheet) as $ch) {
            if ($ch===$char)
                array_push($pos,$i);
            $i++;
        }
        return $pos;
    }

    /**
     * @method clsPos
     * Determines the position of the closing bracket
     * in a Media Query block
     *
     * @return int $i
     */
    public function clsPos()
    {
        $i = 0;
        $k = 0;
        foreach (str_split($this->rawSheet) as $ch) {
            $ch = trim($ch);
            if ($ch==='}') $k++;
            if ($ch!==''&&$ch!=='}') $k = 0;
            if ($k===2) break;
            $i++;
        }
        return $i;
    }

    /**
     * @method charPos
     * Returns the position of the FIRST instance
     * of a certain character in the Stylesheet
     *
     * @param string $char
     * The character that we need to get the FIRST position of
     *
     * @return int when the character is existing
     * @return NULL when the character do not exist
     */
    public function charPos(
        string $char
        )
    {
        return strpos($this->rawSheet,$char) ?: NULL;
    }

    /**
     * @method crop
     * Crops and returns a certain portion of the
     * CSS stylesheet.
     *
     * @param array $coordinates
     * Determines the position where we start and end cropping
     *
     * @return string
     * The final cropped substring
     */
    public function crop(
        array $coordinates
        )
    {
        return substr(
            $this->rawSheet,
            $coordinates[0],
            $coordinates[1] + 1
        );
    }

    /**
     * @method cut
     * Removes a certain block of the CSS Stylesheet
     *
     * @param array $pos
     * Determines the position where we start and end cropping
     *
     */
    public function cut(
        array $pos
        )
    {
        $this->rawSheet = trim(
            substr(
                $this->rawSheet,
                $pos[1] + 1,
                strlen($this->rawSheet)
            )
        );
    }

    /**
     * @method get
     * @return string rawSheet
     * Returns the  CSS stylesheet
     */
    public function get()
    {
        return $this->rawSheet;
    }

    /**
     * @method filter
     * Checkpoint for unsupported CSS Selector
     *
     * NOTE: This might get deprecated in the future updates
     *
     * @return object StyleSheet
     */
    public function filter()
    {
        try {
            if ('.'===$this->fChar())
                goto VALIDATION_PASS;

            if ('@'===$this->fChar())
                goto VALIDATION_PASS;

            if ('/'===$this->fChar())
                goto VALIDATION_PASS;

            throw new \Exception('Unsupported CSS Syntax');

        } catch (\Exception $e) {
            exit();
        }

        VALIDATION_PASS: return $this;

    }

}
