<?php

namespace Kenjiefx\VentaCss\Build\CSS;
use \Kenjiefx\VentaCss\Build\CSS\CSSModel;

class Utils {

    public static function parseRawCss(
        CSSModel $css
        )
    {
        preg_match_all(
            '/(?ims)([a-z0-9*\s\,\.\:#_\-@]+)\{([^\}]*)\}/',
            $css->getRaw(),
            $arr
        );
        foreach ($arr[0] as $i => $x) {
            # Registering a new css selector
            $selector = trim($arr[1][$i]);
            $css->addClass($selector);
            $rules = explode(';', trim($arr[2][$i]));

            foreach ($rules as $strRule) {
                if (!empty($strRule)){
                    $rule = explode(":", $strRule);
                    $css->setAttribute(
                        className: $selector,
                        property: trim($rule[0]),
                        value: trim($rule[1])
                    );
                }
            }
        }
    }

}
