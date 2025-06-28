<?php 

namespace Kenjiefx\VentaCSS\Options;

enum OptionType: string {

    case list = 'list';
    case minmax = 'minmax';
    case count = 'count';
    case dictionary = 'dictionary';
    case breakpoint = 'breakpoint';

}