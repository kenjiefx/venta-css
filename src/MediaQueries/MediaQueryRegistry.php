<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS\MediaQueries;
use Kenjiefx\VentaCSS\VentaConfig;

class MediaQueryRegistry
{

    private static array $array_of_breakpoint_aliases = [];

    public function __construct(
        private VentaConfig $VentaConfig
    ){

    }

    public function register(){
        if (empty(static::$array_of_breakpoint_aliases)) {
            
            $array_of_breakpoint_aliases_from_venta_config = $this->VentaConfig->get_media_breakpoint_aliases();
            foreach($array_of_breakpoint_aliases_from_venta_config as $breakpoint_alias) {
                
                $media_breakpoint_config = $this->VentaConfig->get_media_breakpoint($breakpoint_alias);
                $min_width = $media_breakpoint_config['min-width'];
                $max_width = $media_breakpoint_config['max-width'];
                
                if ($min_width===null&&$max_width!==null) {
                    static::$array_of_breakpoint_aliases[$breakpoint_alias] = [
                        'derived_condition' => '@media only screen and (max-width:'.$max_width.')'
                    ];
                    continue;
                }

                if ($min_width!==null&&$max_width===null) {
                    static::$array_of_breakpoint_aliases[$breakpoint_alias] = [
                        'derived_condition' => '@media only screen and (min-width:'.$min_width.')'
                    ];
                    continue;
                }

                if ($min_width!==null&&$max_width!==null) {
                    static::$array_of_breakpoint_aliases[$breakpoint_alias] = [
                        'derived_condition' => '@media only screen and (min-width:'.$min_width.') and (max-width:'.$max_width.')'
                    ];
                    continue;
                }
            }
        }
        return static::$array_of_breakpoint_aliases;
    }

    public function get_breakpoint_config_by_alias(string $alias){
        if (!isset(static::$array_of_breakpoint_aliases[$alias])) return null;
        return static::$array_of_breakpoint_aliases[$alias];
    }
}
