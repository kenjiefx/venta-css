<?php

namespace Kenjiefx\VentaCss\Build\CSS;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Build\CSS\SelectorModel;


class SelectorMatcher {

    public static function RealSelectorNames(
        SelectorModel $A,
        SelectorModel $B
        )
    {
        return ($A->realName===$B->realName);
    }

    public static function HasPseudoSelectors(
        SelectorModel $A,
        SelectorModel $B
        )
    {
        return ($A->hasPseudo&&$B->hasPseudo);
    }

    public static function PseudoClassNames(
        SelectorModel $A,
        SelectorModel $B
        )
    {
        return ($A->pseudoClass===$B->pseudoClass);
    }


}
