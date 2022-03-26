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
    private array $reference;

    public function __construct(
        Venta $venta
        )
    {
        $this->venta = $venta;
        $this->ParsedCSS = new CSSModel;
        $this->RefinedCss = new CSSModel;
        $this->registrar = [];
        $this->compiled = [];
        $this->reference = [];
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

        $this->reduce();
        $this->sortRegistrar();
        $this->compile();
        $this->release();

        //echo json_encode($this->registrar).PHP_EOL.PHP_EOL;
        //echo json_encode($this->compiled).PHP_EOL.PHP_EOL;
        // echo json_encode($this->reference).PHP_EOL.PHP_EOL;
        // echo json_encode($this->export()).PHP_EOL.PHP_EOL;

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

    /**
     * After the main CSS file was parsed, and individual selectors
     * are given minified name, sorted out, and segragated into separate
     * objects in the Registrar, this method will further reduce the Registrar
     * by eliminating literraly the same selector, but has different rules given
     *
     * Sometimes, when we write CSS, we give different values to the same selectors
     * in the following way:
     *
     * .element {margin: 12px;}
     * .element1, h1 {font-size: 8px;}
     *
     * This method will further reduce the '.element' entries in the Registrar
     * into one CONSOLIDATED registrar entry, like so:
     * "exm":{"rules":{"margin":"12px";"font-size":"12px;""}}
     */
    public function reduce()
    {
        $registrar = $this->registrar;
        $reduced = [];
        foreach ($registrar as $AminifiedName => $AselectorObj) {

            /**
             * Before we save collate existing rules, we will check if the
             * same selector has already been recorded.
             */
            $isExisting = false;
            foreach ($reduced as $RminifiedName => $RselectorObj) {
                if ($RselectorObj->realName===$AselectorObj->realName) {
                    $isExisting = true;
                    break;
                }
            }

            if ($isExisting) continue;

            $matchedRules = [];

            /**
             * On this part of the code, we collate all the existing rules
             * given to the selector of the same name
             */
            foreach ($this->registrar as $BminifiedName => $BselectorObj) {
                if ($AselectorObj->realName==$BselectorObj->realName) {
                    foreach ($BselectorObj->rules as $property => $value) {
                        # Recording each matching rules to consolidate later
                        $matchedRules[$property] = $value;
                    }
                }
            }

            foreach ($matchedRules as $property => $value) {
                $AselectorObj->rules[$property] = $value;
            }

            $reduced[$AminifiedName] = $AselectorObj;
        }

        $this->registrar = $reduced;

    }

    /**
     * Compilation works by eliminating two or more different selectors
     * but litterally has the same rules! A reference dataset will also
     * be compiled so that we will know what CSS selector we gave
     */
    public function compile()
    {

        /**
         * Cases when we  do not include a certain selector in the Registry
         * to our compilation:
         */
        $toCompile = true;

        foreach ($this->registrar as $minifiedName => $selectorObj) {

            $this->addReference($selectorObj->realName,'');

            $matchingRules   = [];
            $unMatchedRules  = $selectorObj->rules;
            $hasMatchingRule = false;


            foreach ($this->compiled as $CminifiedName => $CselectorObj) {

                /**
                 * 1. We can only conclude if the rules has already existed in
                 * our compiled dataset if their selectors are of the same time
                 *
                 * @example
                 * h1 {font:size:10px;} CANNOT combine with .title{font-size:10px;}
                 */
                if ($CselectorObj->typeOf!==$selectorObj->typeOf) continue;

                if ($CselectorObj->hasPseudo)
                {
                    if ($CselectorObj->pseudoClass!==$selectorObj->pseudoClass) continue;
                }

                if (null!==$CselectorObj->childOf) {
                    if ($CselectorObj->childOf!==$selectorObj->pseudoClass) continue;
                }


                $matchingRulesCount = 0;


                foreach ($selectorObj->rules as $property => $value) {
                    $Cvalue = $CselectorObj->rules[$property] ?? null;
                    if ($Cvalue===$value) {
                        $hasMatchingRule = true;
                        unset($unMatchedRules[$property]);
                        $matchingRulesCount++;
                    }
                }


                if ($hasMatchingRule) {

                    /**
                     * When the selector has exactly the same rules with
                     * another selector, but they were given a different
                     * selector name.
                     *
                     * We do not compile this to our final CSS, but we would
                     * reference this in our reference dataset
                     */
                    if ($matchingRulesCount===count($CselectorObj->rules)) {
                        # $this->reference[$selectorObj->realName] .= ' '.$CminifiedName;
                        $this->addReference($selectorObj->realName,$CminifiedName);
                        $toCompile = false;
                        continue;
                    }

                }
            }

            /**
             * Every unmatched rules will be processed under a new selector name
             * @example
             * .wrapper {margin:10px}
             * .container {margin:10px;padding:20px;}
             *
             * The unmatched rule, which is padding:20px, would be compiled
             * into a separate selector, i.e.:
             * .newSelector {padding:20px;}
             *
             */
            if (count($unMatchedRules)>0&&$hasMatchingRule===true) {
                $proxySelector = new SelectorModel($selectorObj->realName);
                $proxySelector->minifyName($this->registrar);
                $proxySelector->rules = $unMatchedRules;
                $this->compiled[$proxySelector->minifiedName] = $proxySelector;
                $this->addReference($selectorObj->realName,$proxySelector->minifiedName);
            }

            if (count($unMatchedRules)===0&&$hasMatchingRule===true) {
                if ($this->reference[$selectorObj->realName]==='') {
                    $this->reference[$selectorObj->realName] = $minifiedName;
                    $this->compiled[$minifiedName] = $selectorObj;
                }
            }


            if (!$hasMatchingRule) {
                $this->reference[$selectorObj->realName] = $minifiedName;
                $this->compiled[$minifiedName] = $selectorObj;
            }


        }
    }

    public function export()
    {
        $forExport = [];
        foreach ($this->compiled as $minifiedName => $selectorObj) {
            if ($selectorObj->hasPseudo) {
                $forExport[$minifiedName.$selectorObj->pseudoSeparator.$selectorObj->pseudoClass] = $selectorObj->rules;
                continue;
            }
            if (null!==$selectorObj->childOf) {
                $parentMinifiedName = $this->rectifyParent($this->reference[$selectorObj->childOf]);
                $forExport[$parentMinifiedName.' '.$minifiedName] = $selectorObj->rules;
                continue;
            }
            if (empty($selectorObj->rules)) {
                continue;
            }
            $forExport[$minifiedName] = $selectorObj->rules;
        }
        return $forExport;
    }

    private function rectifyParent(
        string $parentName
        )
    {
        if (str_contains($parentName,' ')) {
            $tmp = explode(' ',$parentName);
            return implode('.',$tmp);
        }
        return $parentName;
    }


    private function sortRegistrar()
    {
        $scraped = [];
        $sorted = [];
        foreach ($this->registrar as $minifiedName => $selectorObj) {
            $scraped[$minifiedName] = $selectorObj->rules;
        }
        asort($scraped);
        foreach ($scraped as $minifiedName => $value) {
            $sorted[$minifiedName] = $this->registrar[$minifiedName];
        }
        $this->registrar = $sorted;
    }


    public function addReference(
        string $realName,
        string $minifiedName
        )
    {
        if (!isset($this->reference[$realName])) {
            $this->reference[$realName] = '';
        }
        if ($this->reference[$realName]=='') {
            $this->reference[$realName] = $minifiedName;
            return;
        }
        $this->reference[$realName] .= ' '.$minifiedName;
        return;
    }

    public function getReference()
    {
        return $this->references;
    }

    public function release()
    {
        file_put_contents(
            $this->venta->getBackend().'/venta/css.json',
            json_encode($this->reference)
        );
    }

}
