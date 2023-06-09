<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS\Utilities;

class ClassNameMinifierService {

    private const CHARS = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private array $usedNames = [];
    private const NAME_LENGTH = 3;
    private int $name_ext = 1;

    public function create_minified_name_token()
    {
        return $this->generate(
            str_split(ClassNameMinifierService::CHARS)
        );
    }

    private function generate(
        array $chars
        )
    {
        $name = $chars[rand(1,51)].
                $chars[rand(1,51)].
                $chars[rand(1,51)].
                $this->name_ext++;
        if (!in_array($name,$this->usedNames)) {
            array_push($this->usedNames,$name);
            return $name;
        }
        return $this->generate($chars);
    }


}
