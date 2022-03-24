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
    private array $references;

    public function __construct(
        Venta $venta
        )
    {
        $this->venta = $venta;
        $this->ParsedCSS = new CSSModel;
        $this->RefinedCss = new CSSModel;
        $this->references = [];
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

        echo json_encode($this->RefinedCss->export()).PHP_EOL.PHP_EOL;
        echo json_encode($this->references).PHP_EOL.PHP_EOL;
    }


    private function register(
        string $selectorName,
        array $rules
        )
    {
        $selectorObj = new SelectorModel($selectorName);
        if (count($rules)>0) {

            # Register a random class name
            $selectorObj->minifyName($this->RefinedCss->export(),$this->references);
            $this->RefinedCss->createSelector($selectorObj->minifiedName);

            /**
             * A temporary array containing all the possible aggregated
             * selector names
             */
            $selectorNameReferences=[$selectorObj->minifiedName];

            foreach ($this->RefinedCss->export() as $existingSelectorName => $existingRules) {

                $existingSelectorObj = new SelectorModel($existingSelectorName);

                if ($selectorObj->hasPseudo) {
                    /**
                     * Registration process first checks if the current Selector Object
                     * contains pseudo elements.
                     * If the compared-with existing selector do not have, then
                     * we skip the process
                     */
                    if (!$existingSelectorObj->hasPseudo) continue;

                    /**
                     * Secondly, the pseudo-element must be the same for both
                     * selectors.
                     * @example a:hover = a:hover
                     * If not, then skip this process
                     */

                    if ($selectorObj->typeOf!==$existingSelectorObj->typeOf) continue;

                }

                if ($selectorObj->hasChildren) {

                    if (!$existingSelectorObj->hasChildren) continue;
                    if ($selectorObj->parent!==$existingSelectorObj->parent) continue;

                }

                /**
                 * Counting how many rules are matching between the current
                 * registering selector and the current "matched" existing
                 * selector
                 */

                $matchingRules = 0;
                $blacklistedProperties = [];

                foreach ($existingRules as $existingProperty => $existingValue) {

                    if (isset($rules[$existingProperty])&&$rules[$existingProperty]===$existingValue) {
                        $matchingRules++;
                        array_push($blacklistedProperties,$existingProperty);
                    }

                }

                if ($matchingRules===count($existingRules)) {

                    /**
                     * Avoiding adding the same reference name
                     */
                    if (!in_array($existingSelectorName,$selectorNameReferences)) {
                        array_push($selectorNameReferences,$existingSelectorName);
                    }

                    foreach ($blacklistedProperties as $blacklistedProperty) {
                        /**
                         * NOTE: Setting rule to NULL
                         * On the later part, properties that was CONVERTED to NULL
                         * would be skipped in the final registration of the CSS
                         * Selector
                         */
                        $rules[$blacklistedProperty] = NULL;
                    }
                }

            }

            /**
             * Saving the remaining unmatched
             * properties and values as a single
             * selector
             */
            $isAllPropertyAndValuesMatched = true;

            foreach ($rules as $property => $value) {
                if ($value!==NULL) {
                    $isAllPropertyAndValuesMatched = false;
                    $this->RefinedCss->setAttribute(
                         $selectorObj->minifiedName,
                         $property,
                         $value
                     );
                }
            }

            /**
             * This is when the properties and values were ALL MATCHED
             * then we do not need to register a new CSS selector
             */
            if ($isAllPropertyAndValuesMatched) {
                unset($selectorNameReferences[0]);
                $this->RefinedCss->removeSelector($selectorObj->minifiedName);
            }

            $this->references[$selectorName] = implode(' ',$selectorNameReferences);

        }
        unset($class);
    }







}
