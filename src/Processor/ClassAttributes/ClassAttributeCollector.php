<?php 

namespace Kenjiefx\VentaCSS\Processor\ClassAttributes;

class ClassAttributeCollector {

    public const CLASS_ATTRIBUTE = 'class="';

    public function __construct(

    ) {}

    /**
     * Collects class attribute from an HTML string and returns an array of class names.
     *
     * @param string $html An HTML string containing a class attribute.
     * @return ClassAttributeIterator An iterator containing ClassAttributeModel objects for each class attribute found.
     */
    public function collect(string $html): ClassAttributeIterator
    {
        $classAttributeModels = [];
        $attrs = str_split(Self::CLASS_ATTRIBUTE);
        $pointer = 0;
        $recording = false;
        $classes = '';
        foreach (str_split($html) as $htmlChar) {
            if ($recording && $htmlChar!=='"') {
                $classes = $classes.$htmlChar;
                continue;
            }
            if ($recording && $htmlChar==='"') {
                $classAttributeModel = 
                    new ClassAttributeModel(
                        trim($classes)
                    );
                array_push($classAttributeModels, $classAttributeModel);
                $classes = '';
                $recording = false;
                $pointer = 0;
                continue;
            }
            ($htmlChar === $attrs[$pointer]) ? $pointer++ : $pointer = 0;
            if ($pointer===count($attrs)) {
                $recording = true;
            }
        }
        return new ClassAttributeIterator($classAttributeModels);
    }
}