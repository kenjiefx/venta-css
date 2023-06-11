<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS\Utilities;

class ClassNameMinifierService {

    private const CHARS = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private static array $used_names = [];

    public function create_minified_name_token()
    {
        return $this->generate(
            str_split(ClassNameMinifierService::CHARS)
        );
    }

    private function generate(
        array $chars,
        int $name_ext = 0,
        $name = null
        )
    {
        if ($name===null) {
            $name = $chars[rand(1,51)].
                    $chars[rand(1,51)].
                    $chars[rand(1,51)];
        }
        $name .= $name_ext++;
        if (!in_array($name,static::$used_names)) {
            array_push(static::$used_names,$name);
            return $name;
        }
        return $this->generate($chars,$name_ext,$name);
    }

    public function clear_utilized_minified_names(){
        static::$used_names = [];
    }


}
