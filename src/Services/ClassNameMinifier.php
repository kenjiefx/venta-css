<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS\Services;

class ClassNameMinifier {

    private const CHARS = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private array $usedNames = [];
    private const NAME_LENGTH = 3;

    public function create()
    {
        $this->nameExt = 1;
        return $this->generate(
            str_split(ClassNameMinifier::CHARS)
        );
    }

    private function generate(
        array $chars
        )
    {
        $name = $chars[rand(1,51)].
                $chars[rand(1,51)].
                $chars[rand(1,51)].
                $this->nameExt++;
        if (!in_array($name,$this->usedNames)) {
            array_push($this->usedNames,$name);
            return $name;
        }
        return $this->generate($chars);
    }


}
