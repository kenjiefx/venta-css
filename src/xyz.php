<?php

// CSS source file
$path = 'test.css';
$css = file_get_contents($path);

$htmlPath = 'sample.html';
$html = file_get_contents($htmlPath);
// Contains all reference CSS for parsing HTML files
$GLOBALS['cssRef'] = [];

// Contains all final CSS rules
$GLOBALS['cssBank'] = [];

// Returns a new, 3-letter class Name
function createClassName(){
  $firsts = 'abcdefghijklmnopqrstuvwxyz';
  $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
  $isValid = true;
  $first = substr(str_shuffle($firsts),(-1));
  $name = $first.substr(str_shuffle($chars),(-2));
  if(isset($GLOBALS['cssBank'][$name])) $isValid = false;
  while(!$isValid) createClassName($GLOBALS['cssBank']);
  return $name;
}

// Registering CSS rules
function registerRule($selector,$rules){

  // Generate a new classname
  $className = createClassName();

  // Counts how many of the CSS selector already existing
  $match = false;

  // Records all matching class names
  $matchedClassNames = [];

  // A copy of the current registering rule
  $mirrored = $rules;

  foreach ($rules as $property => $value) {

    /* Check whether this property and value already registered
      in a certain class name */
    foreach ($GLOBALS['cssBank'] as $existingClassName => $existingRules) {

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
        $GLOBALS['cssBank'][$className][$property] = $value;
      }
    }

    // Compiling all class names to be served as a reference
    array_push($matchedClassNames,$className);
    $compiled = implode(' ',$matchedClassNames);

    $GLOBALS['cssRef'][ltrim($selector, '.')] = $compiled;

    return;

  }

  // If there is no pre-existing rules that matches
  foreach ($rules as $property => $value) {
    $GLOBALS['cssBank'][$className][$property] = $value;
  }

  $GLOBALS['cssRef'][ltrim($selector, '.')] = $className;
  return;

}


// Parses a css file, turns it into array of css selector -> rules
function parseCss($rawCssFile){
  preg_match_all( '/(?ims)([a-z0-9\s\,\.\:#_\-@]+)\{([^\}]*)\}/',$rawCssFile,$arr);
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
      registerRule($selector,$rules);
    }
  }
  // Mapping of the Rules
  // echo json_encode($GLOBALS['cssBank']).PHP_EOL;
  //PHP_EOL;
  //echo json_encode($GLOBALS['cssRef']).PHP_EOL;
}


function findClasses($str){
  $re = '/class="\s*(.*?)\s*"/s';
  preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
  return $matches;
}

function parseHTML($html){
  $re = '/class="\s*(.*?)\s*"/s';
  preg_match_all($re, $html, $matches, PREG_SET_ORDER, 0);
  foreach ($matches as $key => $value) {
    $classes = explode(' ',$value[1]);
    $aggregatedClasses = [];
    foreach ($classes as $class) {
      if (isset($GLOBALS['cssRef'][$class])) {
        $cssRefs = explode(' ',$GLOBALS['cssRef'][$class]);
        foreach ($cssRefs as $cssRef) {
          if (!in_array($cssRef,$aggregatedClasses)) {
            array_push($aggregatedClasses,$cssRef);
          }
        }
        //$aggregatedClasses = $GLOBALS['cssRef'][$class].' '.$aggregatedClasses;
      } else {
        if (!in_array($class,$aggregatedClasses)) {
          array_push($aggregatedClasses,$class);
        }
        //$aggregatedClasses = $class.' '.$aggregatedClasses;
      }
    }
    $compressedClassNames = implode(' ',$aggregatedClasses);
    $parsed = str_replace($value[0],'class="'.$compressedClassNames.'"',$html);
    $html = $parsed;
  }
}



parseCss($css);
parseHTML($html);

// Converting CSS
$exportCssPath = 'x.css';
$exportCss = '';
foreach ($GLOBALS['cssBank'] as $className => $properties) {
  $exportCss = $exportCss.'.'.$className.' { ';
  foreach ($properties as $property => $value) {
    $exportCss = $exportCss.$property.': '.$value.'; ';
  }
  $exportCss = $exportCss.'} ';
}

file_put_contents($exportCssPath,$exportCss);



// Converting Reference CSS
$exportCssPath = 'z.css';

file_put_contents($exportCssPath,json_encode($GLOBALS['cssRef']));
