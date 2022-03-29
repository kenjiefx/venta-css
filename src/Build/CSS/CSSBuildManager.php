<?php

namespace Kenjiefx\VentaCss\Build\CSS;
use \Kenjiefx\VentaCss\Cli\CoutStreamer;
use \Kenjiefx\VentaCss\Venta\Venta;
use \Kenjiefx\VentaCss\Build\CSS\DataLake;
use \Kenjiefx\VentaCss\Build\CSS\CSSModel;
use \Kenjiefx\VentaCss\Build\CSS\ClassModel;
use \Kenjiefx\VentaCss\Build\CSS\SelectorModel;
use \Kenjiefx\VentaCss\Build\CSS\Utils;
use \Kenjiefx\VentaCss\Build\CSS\SelectorMatcher as Matching;

class CSSBuildManager {

    private Venta $venta;
    private CSSModel $ParsedCSS;
    private CSSModel $RefinedCss;
    private string $css;
    private array $theRegistrar;
    private array $theTracker;
    private array $compiled;
    private array $reference;

    public function __construct(
        Venta $venta
        )
    {
        $this->venta = $venta;
        $this->ParsedCSS = new CSSModel;
        $this->RefinedCss = new CSSModel;
        $this->theRegistrar = [];
        $this->theTracker = [];
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

        CoutStreamer::cout('Compressing class names...');
        $this->reduce();

        $this->sortRegistrar();

        echo json_encode($this->theRegistrar).PHP_EOL.PHP_EOL;
        exit();

        $this->compile();

        CoutStreamer::cout('Saving venta/app.css...');
        $this->release();

        // echo json_encode($this->compiled).PHP_EOL.PHP_EOL;
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
                $selectorObj->rules = [];

                # Registering parent name and child name
                if (null!==$childOf) {
                    $selectorObj->childOf = new SelectorModel($childOf);
                }

                # Making sure that only the last child declared in the selector
                # Will register the rules given to that selector
                if ($familyMemberIterator===$numberOfFamilyMember) {
                    $selectorObj->rules = $rules;
                }

                $childOf = trim($selector);

                # Saving the Selector object to the Registrar array
                array_push($this->theRegistrar,$selectorObj);

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
        $TheRegistrar = $this->theRegistrar;
        $reduced = [];

        foreach ($TheRegistrar as $A) {

            /**
             * Before we save collate existing rules, we will check if the
             * same selector has already been recorded.
             *
             * The rules for two selectors to be considered as the same
             * are the following
             * 1. They must have the same pseudo type
             */
            $isExisting = false;
            $A->minifyName($this->theTracker);

            foreach ($reduced as $key => $R) {
                if (Matching::RealSelectorNames($A,$R))
                    $A->setMinifiedName($R->minifiedName);
                if (!Matching::RealSelectorNames($A,$R))
                    continue;
                if (!Matching::PseudoClassNames($A,$R))
                    continue;
                foreach ($A->rules as $property => $value)
                    $reduced[$key]->rules[$property] = $value;
                $isExisting = true;
                break;
            }

            if (!$isExisting)
                array_push($reduced,$A);
        }

        $this->theRegistrar = $reduced;

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

        foreach ($this->theRegistrar as $minifiedName => $selectorObj) {

            $this->addReference(
                $selectorObj->realName,
                '',
                $selectorObj->prefixer,
                $selectorObj->typeOf,
                $selectorObj->groupName
            );

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


                if ($CselectorObj->hasPseudo||$selectorObj->hasPseudo)
                {
                    if (!$selectorObj->hasPseudo) continue;
                    if (!$CselectorObj->hasPseudo) continue;
                    if ($CselectorObj->pseudoClass!==$selectorObj->pseudoClass) continue;
                }

                if (null!==$CselectorObj->childOf&&null!==$selectorObj->childOf) {
                    if ($CselectorObj->childOf->realName!==$selectorObj->childOf->realName) continue;
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
                        $this->addReference(
                            $selectorObj->realName,
                            $CminifiedName,
                            $CselectorObj->prefixer,
                            $CselectorObj->typeOf,
                            $CselectorObj->groupName
                        );
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
                $proxySelector->minifyName($this->theRegistrar);
                $proxySelector->rules = $unMatchedRules;
                $this->compiled[$proxySelector->minifiedName] = $proxySelector;
                $this->addReference(
                    $selectorObj->realName,
                    $proxySelector->minifiedName,
                    $proxySelector->prefixer,
                    $proxySelector->typeOf,
                    $selectorObj->groupName
                );
            }

            if (count($unMatchedRules)===0&&$hasMatchingRule===true) {
                if ($this->reference[$selectorObj->realName]['html']==='') {
                    $this->addReference(
                        $selectorObj->realName,
                        $minifiedName,
                        $selectorObj->prefixer,
                        $selectorObj->typeOf,
                        $selectorObj->groupName
                    );
                    $this->compiled[$minifiedName] = $selectorObj;
                }
            }


            if (!$hasMatchingRule) {
                $this->addReference(
                    $selectorObj->realName,
                    $minifiedName,
                    $selectorObj->prefixer,
                    $selectorObj->typeOf,
                    $selectorObj->groupName
                );
                $this->compiled[$minifiedName] = $selectorObj;
            }


        }
    }

    public function export()
    {
        $forExport = [];
        foreach ($this->compiled as $minifiedName => $selectorObj) {
            if ($selectorObj->groupName!==null) {
                $minifiedName = $selectorObj->prefixer.$selectorObj->groupName;
            } else {
                $minifiedName = $selectorObj->prefixer.$minifiedName;
            }
            if ($selectorObj->hasPseudo) {
                $forExport[$minifiedName.$selectorObj->pseudoSeparator.$selectorObj->pseudoClass] = $selectorObj->rules;
                continue;
            }
            if (null!==$selectorObj->childOf) {
                $parentMinifiedName = $this->reference[$selectorObj->childOf->realName]['css'];
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


    private function sortRegistrar()
    {
        $scraped = [];
        $sorted = [];
        foreach ($this->theRegistrar as $key => $Sobj) {
            $scraped[$key.'x'] = $Sobj->rules;
        }
        asort($scraped);
        foreach ($scraped as $key => $value) {
            $rKey = intval($key[0]);
            array_push($sorted,$this->theRegistrar[$rKey]);
        }
        $this->theRegistrar = $sorted;
    }


    public function addReference(
        string $realName,
        string $minifiedName,
        string $prefixer,
        string $typeOf,
        string $groupName = null
        )
    {
        if (null!==$groupName) {
            $minifiedName = $groupName;
        }
        if (!isset($this->reference[$realName])) {
            $this->reference[$realName] = [
                'html' => '',
                'css' => '',
                'typeOf' => ''
            ];
        }
        if ($this->reference[$realName]['html']=='') {
            $this->reference[$realName] = [
                'html' => $minifiedName,
                'css' => $prefixer.$minifiedName,
                'typeOf' => $typeOf
            ];
            return;
        }
        $this->reference[$realName] = [
            'html' => $this->reference[$realName]['html'].' '.$minifiedName,
            'css' => $this->reference[$realName]['css'].$prefixer.$minifiedName,
            'typeOf' => $typeOf
        ];
        return;
    }

    public function getReference()
    {
        return $this->references;
    }

    public function release()
    {

        file_put_contents(
            $this->venta->getBackend().'/venta/__venta.css.json',
            json_encode($this->reference)
        );
        file_put_contents(
            $this->venta->getBackend().'/venta/__venta.map.json',
            json_encode($this->export())
        );
    }

}
