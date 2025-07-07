<?php 

namespace Kenjiefx\VentaCSS\Classes;

class ClassRegistry {

    private static array $registry = [];

    private static array $themes = [];

    public function __construct(

    ) {}

    public function register(ClassKey $key, string $theme, ClassModel $classModel) {
        // Override existing entry if it exists
        self::$registry[(string)$key] = $classModel;
        if (!in_array($theme, static::$themes)) {
            array_push(static::$themes, $theme);
        }
    }

    public function getAll(): ClassIterator {
        $arrayOfClasses = [];
        foreach (self::$registry as $classModel) {
            if ($classModel instanceof ClassModel) {
                $arrayOfClasses[] = $classModel;
            }
        }
        return new ClassIterator($arrayOfClasses);
    }

    public function lookup(string $property, string $variant): ClassIterator {
        $arrayOfClasses = [];
        foreach (static::$themes as $theme) {
            $key = new ClassKey(
                property: $property,
                variant: $variant,
                theme: $theme
            );
            if (isset(static::$registry[(string)$key])) {
                array_push($arrayOfClasses, static::$registry[(string)$key]);
            }
        }
        return new ClassIterator($arrayOfClasses);
    }

}