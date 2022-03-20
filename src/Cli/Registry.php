<?php

namespace Kenjiefx\VentaCss\Cli;

class Registry {

  public static function get(
    $argv
    )
  {
    $arg = $argv[1] ?? null;
    switch ($arg) {
      case 'build':
        return 'build';
        break;
      case '--v':
        return '--v';
        break;
      case 'hook':
        return 'hook';
        break;
      case 'revert':
        return 'revert';
        break;
      default:
        return null;
        break;
    }
  }

}
