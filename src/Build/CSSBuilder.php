<?php

namespace Kenjiefx\VentaCss\Build;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;

class CSSBuilder {

  private string $namespace;
  private string $path;
  private string $css;
  private array $cssBank;
  private array $cssRef;

  public function __construct(
    $namespace
    )
  {
    $this->namespace = $namespace;
    $this->path = ROOT.'/'.$this->namespace.'/venta/app.css';
    try {
      if (!file_exists($this->path)) {
        throw new \Exception('Unable to find /venta/app.css in /'.$this->namespace.' directory', 1);
      }
    } catch (\Exception $e) {
      CoutStreamer::cout('Error: '.$e->getMessage(),'error');
      exit();
    }
    $this->css = file_get_contents($this->path);
    $this->cssBank = [];
    $this->cssRef = [];
  }

  public function createClassName(){
    $firsts = 'abcdefghijklmnopqrstuvwxyz';
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $isValid = true;
    $first = substr(str_shuffle($firsts),(-1));
    $name = $first.substr(str_shuffle($chars),(-2));
    if(isset($this->cssBank[$name])) $isValid = false;
    while(!$isValid) createClassName();
    return $name;
  }

  public function compile()
  {
    preg_match_all( '/(?ims)([a-z0-9*\s\,\.\:#_\-@]+)\{([^\}]*)\}/',$this->css,$arr);
    $selectors = array();
    foreach ($arr[0] as $i => $x) {
      $selector = trim($arr[1][$i]);
      $rules = explode(';', trim($arr[2][$i]));
      $selectors[$selector] = array();
      foreach ($rules as $strRule) {
        if (!empty($strRule)){
          $rule = explode(":", $strRule);
          $selectors[$selector][trim($rule[0])] = trim($rule[1]);
        }
      }
    }
    asort($selectors);

    foreach ($selectors as $selector => $rules) {
      if (count($rules)>0) {
        $this->registerRule($selector,$rules);
      }
    }

    // Mapping of the Rules
    // echo json_encode($this->cssBank).PHP_EOL;
    //PHP_EOL;
    //echo json_encode($GLOBALS['cssRef']).PHP_EOL;
  }

  private function registerRule($selector,$rules)
  {

      $withPseudoClass = false;
      $pseudoClass = '';

      // Generate a new classname
      if ($selector==='*') {
        $className = '*';
      }
      elseif (str_contains($selector,':')) {
        $right = explode(':',$selector)[1];
        $className = $this->createClassName().':'.$right;
        $withPseudoClass = true;
        $pseudoClass = $right;
      }
      else {
        $className = $this->createClassName();
      }

      // Counts how many of the CSS selector already existing
      $match = false;
      // Records all matching class names
      $matchedClassNames = [];
      // A copy of the current registering rule
      $mirrored = $rules;
      foreach ($rules as $property => $value) {

        /* Check whether this property and value already registered
          in a certain class name */
        foreach ($this->cssBank as $existingClassName => $existingRules) {

          if ($withPseudoClass) {
            if (!str_contains($existingClassName,':')) {
              continue;
            }
            $existingClassNamePseudoClass = explode(':',$existingClassName)[1];
            if ($existingClassNamePseudoClass!==$pseudoClass) {
              continue;
            }
          }

          $existingNumOfProperties = count($existingRules);
          $matchingProperties = 0;
          $blacklistedProperties = [];

          foreach ($existingRules as $existingProperty => $existingValue) {

            if (isset($rules[$existingProperty])) {
              // Property and value must both match / exactly the same
              if ($rules[$existingProperty]==$existingValue) {
                array_push($blacklistedProperties,$existingProperty);
                $matchingProperties++;
              }
            }

          }

          if ($existingNumOfProperties==$matchingProperties) {
            if (!in_array($existingClassName,$matchedClassNames)) {
              array_push($matchedClassNames,$existingClassName);
            }
            $match = true;
            foreach ($blacklistedProperties as $blacklistedProperty) {
              $mirrored[$blacklistedProperty] = 'rule existing';
            }
          }

        }
      }

      // If there is a match
      if ($match){

        // Registers the remaining rule combination that has no matches
        foreach ($mirrored as $property => $value) {
          if ($value!=='rule existing') {
            $this->cssBank[$className][$property] = $value;
          }
        }

        // Compiling all class names to be served as a reference
        array_push($matchedClassNames,$className);
        $compiled = implode(' ',$matchedClassNames);

        if (str_contains($selector,':')) {
          $cleanedMatchedNames = [];
          foreach ($matchedClassNames as $matchedClassName) {
            if (str_contains($matchedClassName,':')) {
              $cleanedUp = explode(':',$matchedClassName)[0];
              array_push($cleanedMatchedNames,$cleanedUp);
            } else {
              array_push($cleanedMatchedNames,$matchedClassName);
            }
          }
          $compiled = implode(' ',$cleanedMatchedNames);
          if (str_contains($selector,':')) {
            $selector = explode(':',$selector)[0];
          }
          $this->cssRef[ltrim($selector, '.')] = $compiled;
          return;
        }
        $this->cssRef[ltrim($selector, '.')] = $compiled;
        return;
    }

    // If there is no pre-existing rules that matches
    foreach ($rules as $property => $value) {
      $this->cssBank[$className][$property] = $value;
    }

    if (str_contains($selector,':')) {
      $selector = explode(':',$selector)[0];
    }
    if (str_contains($className,':')) {
      $className = explode(':',$className)[0];
    }
    $this->cssRef[ltrim($selector, '.')] = $className;
    return;

  }

  private function export()
  {
    $exportCss = '';
    foreach ($this->cssBank as $className => $properties) {
      if ($className==='*') {
        $exportCss = $exportCss.$className.'{';
      } else {
        $exportCss = $exportCss.'.'.$className.'{';
      }
      foreach ($properties as $property => $value) {
        $exportCss = $exportCss.$property.':'.$value.';';
      }
      $exportCss = $exportCss.'}';
    }
    return $exportCss;
  }

  public function save()
  {
    $build = $this->export();
    file_put_contents($this->path,$build);
    file_put_contents(ROOT.'/vnt/'.$this->namespace.'/venta/css.json',json_encode($this->cssRef));
  }


}
