<?php

namespace Kenjiefx\VentaCSS\Options;

use InvalidArgumentException;

class OptionFactory
{
    public function __construct() {}

    public function create(
        string $theme,
        string $property, 
        array $option
    ): OptionModel
    {
        $this->validateStructure($option);

        $type = OptionType::from($option['type']);

        $this->validateValuesByType($type, $option['values']);

        return new OptionModel(
            theme: $theme,
            type: $type,
            property: $property,
            rule: $option['rule'],
            values: $option['values']
        );
    }

    private function validateStructure(array $option): void
    {
        foreach (['type', 'rule', 'values'] as $key) {
            if (!isset($option[$key])) {
                throw new InvalidArgumentException("Missing required option field: '$key'.");
            }
        }

        if (!OptionType::tryFrom($option['type'])) {
            throw new InvalidArgumentException("Invalid option type provided: '{$option['type']}'.");
        }
    }

    private function validateValuesByType(OptionType $type, mixed $values): void
    {
        if (!is_array($values)) {
            throw new InvalidArgumentException("Option type '{$type->value}' requires 'values' to be an array.");
        }

        match ($type) {
            OptionType::list => $this->validateList($values),
            OptionType::count => $this->validateCount($values),
            OptionType::minmax => $this->validateMinMax($values),
            OptionType::dictionary => $this->validateAssociativeArray($values, 'dictionary'),
            OptionType::breakpoint => $this->validateBreakpoint($values),
            default => null,
        };
    }

    private function validateList(array $values): void
    {
        if (array_values($values) !== $values) {
            throw new InvalidArgumentException("Option type 'list' requires a one-dimensional array.");
        }
    }

    private function validateCount(array $values): void
    {
        $count = count($values);
        if ($count === 0 || $count > 2) {
            throw new InvalidArgumentException("Option type 'count' requires a non-empty array with up to 2 elements.");
        }

        if (!array_reduce($values, fn($carry, $item) => $carry && is_int($item) && $item >= 0, true)) {
            throw new InvalidArgumentException("Option type 'count' requires whole numbers.");
        }

        if ($count === 2 && $values[0] >= $values[1]) {
            throw new InvalidArgumentException("Option type 'count' requires the first number to be smaller than the second.");
        }
    }

    private function validateMinMax(array $values): void
    {
        $count = count($values);
        if ($count === 0 || $count > 3) {
            throw new InvalidArgumentException("Option type 'minmax' requires 1 to 3 elements.");
        }

        if (!array_reduce($values, fn($carry, $item) => $carry && is_numeric($item), true)) {
            throw new InvalidArgumentException("Option type 'minmax' requires integer values.");
        }
    }

    private function validateAssociativeArray(array $values, string $typeLabel): void
    {
        if (array_values($values) === $values) {
            throw new InvalidArgumentException("Option type '$typeLabel' requires an associative array with keys and values.");
        }
    }

    private function validateBreakpoint(array $values): void
    {
        $this->validateAssociativeArray($values, 'breakpoint');

        if (!isset($values['min-width']) || !isset($values['max-width'])) {
            throw new InvalidArgumentException("Option type 'breakpoint' requires 'min-width' and 'max-width' keys.");
        }
    }
}
