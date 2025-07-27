<?php 

namespace Kenjiefx\VentaCSS\Variables;

use Kenjiefx\VentaCSS\Classes\ClassModel;
use Kenjiefx\VentaCSS\Classes\ClassRegistry;

class RootVariableRegistry {

    private static array $variables = [];

    public function __construct(
        public readonly ClassRegistry $classRegistry
    ) {}

    public function build() {
        $allRegisteredClasses = $this->classRegistry->getAll();
        foreach ($allRegisteredClasses as $classModel) {
            $this->register($this->createKey($classModel), $classModel);
        }
    }

    public function createKey(ClassModel $classModel) {
        $variant = $classModel->key->variant;
        $theme = $classModel->key->theme;
        $name = $classModel->key->property;
        return "--{$theme}-{$name}-{$variant}";
    }

    public function register(string  $variableKey, ClassModel $classModel) {
        if (!isset(static::$variables[$variableKey])) {
            static::$variables[$variableKey] = $classModel;
        }
    }

    public function getByKey(string $variableKey): ?ClassModel {
        return static::$variables[$variableKey] ?? null;
    }

    public function getAll() {
        return static::$variables;
    }

    public function clear() {
        static::$variables = [];
    }

}