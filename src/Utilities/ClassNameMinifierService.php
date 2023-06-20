<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS\Utilities;

class ClassNameMinifierService {

    private const CHARS = 'abcdefghijklmnOPQRSTUVWXYZ';
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
        string|null $base_name = null
        )
    {
        if ($base_name===null) {
            $base_name = $chars[rand(0,25)].
                         $chars[rand(0,25)].
                         $chars[rand(0,25)];
        }
        $minified_name = ($name_ext>0) ? $base_name.$name_ext : $base_name;
        if (!in_array($minified_name,static::$used_names)) {
            array_push(static::$used_names,$minified_name);
            return $minified_name;
        }
        $name_ext++;
        return $this->generate($chars,$name_ext,$base_name);
    }

    public function clear_utilized_minified_names(){
        static::$used_names = [];
    }


}
