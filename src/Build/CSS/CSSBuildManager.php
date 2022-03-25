<?php

namespace Kenjiefx\VentaCss\Build\CSS;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Venta\Venta;
use \Kenjiefx\VentaCss\Build\CSS\DataLake;
use \Kenjiefx\VentaCss\Build\CSS\CSSModel;
use \Kenjiefx\VentaCss\Build\CSS\ClassModel;
use \Kenjiefx\VentaCss\Build\CSS\SelectorModel;
use \Kenjiefx\VentaCss\Build\CSS\Utils;

class CSSBuildManager {

    private Venta $venta;
    private CSSModel $ParsedCSS;
    private CSSModel $RefinedCss;
    private string $css;
    private array $registrar;
    private array $compiled;

    public function __construct(
        Venta $venta
        )
    {
        $this->venta = $venta;
        $this->ParsedCSS = new CSSModel;
        $this->RefinedCss = new CSSModel;
        $this->registrar = [];
        $this->compiled = [];
    }

    public function build()
    {
        # First, we set the raw CSS file: venta/app.css
        $this->ParsedCSS->setRaw(
            rawCss: $this->venta->getCssToBuild()
        );

        # Next, we parse the raw CSS into an array
        Utils::parseRawCss($this->ParsedCSS);

        # Then, we sort the CSS array
        $this->ParsedCSS->sort();

        # Then, we register each of the CSS class
        foreach ($this->ParsedCSS->export() as $selector => $rules) {
            $this->register($selector,$rules);
        }

        $this->compile();

        echo json_encode($this->compiled).PHP_EOL.PHP_EOL;
        echo json_encode($this->registrar).PHP_EOL.PHP_EOL;
    }


    private function register(
        string $selectorName,
        array $rules
        )
    {
        # Parsing every selector entity in a group of selectors
        $selectorGroups = explode(',',$selectorName);

        foreach ($selectorGroups as $selectorGroup) {

            # Parsing every selector entity in a family of selectors
            $selectorFamily = explode(' ',trim($selectorGroup));

            $parentOf = null;
            $childOf = null;

            $numberOfFamilyMember = count($selectorFamily);
            $familyMemberIterator = 1;

            /**
             * Looping through each of the family members!
             * We neen to make sure that in a certain CSS selector
             * only the last child declared will receive the rules.
             * Here, we also make sure that every family generation
             * is registered in the registrar array
             */
            foreach ($selectorFamily as $selector) {

                $selectorObj = new SelectorModel(trim($selector));
                $selectorObj->minifyName($this->registrar);
                $selectorObj->rules = [];

                # Registering parent name and child name
                $selectorObj->parentOf = $parentOf;
                $selectorObj->childOf = $childOf;

                # Making sure that only the last child declared in the selector
                # Will register the rules given to that selector
                if ($familyMemberIterator===$numberOfFamilyMember) {
                    $selectorObj->rules = $rules;
                }

                $childOf = trim($selector);

                # Saving the Selector object to the Registrar array
                $this->registrar[$selectorObj->minifiedName] = $selectorObj;

                $familyMemberIterator++;

            }
        }
    }

    public function compile()
    {
        foreach ($this->registrar as $selectorObj) {

            $toCompileRules = $selectorObj->rules;

            foreach ($this->compiled as $rules) {

                if (!empty($toCompileRules)) {

                    $existingRules = $this->findExistingRules(
                        $selectorObj->rules,
                        $rules
                    );

                    foreach ($existingRules as $property => $value) {
                        unset($toCompileRules[$property]);
                    }
                }

            }

            $this->compiled[$selectorObj->minifiedName] = $toCompileRules;

            # Skipping to compile any selector that has empty rules
            if (empty($this->compiled)) {
                $this->compiled[$selectorObj->minifiedName] = $selectorObj->rules;
                return;
            }

            //$this->compiled[$selectorObj->minifiedName] = $toCompileRules;


        }
    }

    public function findExistingRules(
        array $selector1,
        array $selector2
        )
    {
        $existingRules = [];
        foreach ($selector1 as $property => $value) {
            if (isset($selector2[$property])) {
                if ($selector2[$property]===$value) {
                    $existingRules[$property] = $value;
                }
            }
        }
        return $existingRules;
    }

    public function getReference()
    {
        return $this->references;
    }







}
