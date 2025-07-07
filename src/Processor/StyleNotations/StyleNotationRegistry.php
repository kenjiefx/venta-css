<?php 

namespace Kenjiefx\VentaCSS\Processor\StyleNotations;

class StyleNotationRegistry {

    private static array $registry = [];

    public function __construct(

    ) {}

    public function registerIfNotExist(
        StyleNotationModel $styleNotationModel
    ) {
        $value = $styleNotationModel->value;
        if (!isset(static::$registry[$value])) {
            static::$registry[$value] = $styleNotationModel;
        }
    }

    public function getAll(): StyleNotificationIterator {
        $registry = [];
        foreach (static::$registry as $notation => $styleNotationModel) {
            $registry[] = $styleNotationModel;
        }
        return new StyleNotificationIterator($registry);
    }

    public function lookupByNotation(
        string $styleNotation
    ): ?StyleNotationModel {
        return static::$registry[$styleNotation] ?? null;
    }

    public function clearExisting() {
        static::$registry = [];
    }

}