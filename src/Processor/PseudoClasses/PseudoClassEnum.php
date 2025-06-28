<?php 

namespace Kenjiefx\VentaCSS\Processor\PseudoClasses;

enum PseudoClassEnum {

    case ACTIVE;
    case CHECKED;
    case DISABLED;
    case FOCUS;
    case HOVER;
    case LINK;
    case VISITED;

    public function toString(): string
    {
        return match ($this) {
            self::ACTIVE => ':active',
            self::CHECKED => ':checked',
            self::DISABLED => ':disabled',
            self::FOCUS => ':focus',
            self::HOVER => ':hover',
            self::LINK => ':link',
            self::VISITED => ':visited',
        };
    }

    public static function fromString(string $pseudo): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->toString() === $pseudo) {
                return $case;
            }
        }
        return null;
    }

}