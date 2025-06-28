<?php 

use Kenjiefx\VentaCSS\Breakpoints\BreakpointModel;
use Kenjiefx\VentaCSS\Breakpoints\BreakpointRegistry;
use Kenjiefx\VentaCSS\Classes\ClassKey;
use Kenjiefx\VentaCSS\Classes\ClassModel;
use Kenjiefx\VentaCSS\Classes\ClassRegistry;
use Kenjiefx\VentaCSS\Processor\ClassAttributes\ClassAttributeCollector;
use Kenjiefx\VentaCSS\Processor\HtmlContentProcessor;
use Kenjiefx\VentaCSS\Processor\StyleNotations\StyleNotationFactory;
use Kenjiefx\VentaCSS\Processor\StyleNotations\StyleNotationRegistry;
use Kenjiefx\VentaCSS\Tokens\MinifiedTokenPool;

$classRegistry = new ClassRegistry();
$classKey1 = new ClassKey(property: "display", variant: "flex", theme: "default");
$classRegistry->register(
    key: $classKey1,
    theme: "default",
    classModel: new ClassModel(
        key: $classKey1,
        declaration: "display:flex;"
    )
);
$classKey2 = new ClassKey(property: "width", variant: "23", theme: "default");
$classRegistry->register(
    key: $classKey2,
    theme: "default",
    classModel: new ClassModel(
        key: $classKey2,
        declaration: "width:293px;"
    )
);
$classKey3 = new ClassKey(property: "color", variant: "black", theme: "default");
$classRegistry->register(
    key: $classKey3,
    theme: "default",
    classModel: new ClassModel(
        key: $classKey3,
        declaration: "color:black;"
    )
);
$classKey4 = new ClassKey(property: "color", variant: "white", theme: "dark");
$classRegistry->register(
    key: $classKey4,
    theme: "dark",
    classModel: new ClassModel(
        key: $classKey4,
        declaration: "color:white;"
    )
);
$classKey5 = new ClassKey(property: "display", variant: "none", theme: "default");
$classRegistry->register(
    key: $classKey5,
    theme: "default",
    classModel: new ClassModel(
        key: $classKey5,
        declaration: "display:none;"
    )
);

$desktopBreakpoint = new BreakpointModel(
    name: "desktop",
    mediaQuery: "@media (min-width: 1024px)"
);
$breakpointRegistry = new BreakpointRegistry();
$breakpointRegistry->register("desktop", $desktopBreakpoint);

// This is a stubbed version of the MinifiedTokenPool for testing purposes.
class StubbedMinifiedTokenPool extends MinifiedTokenPool {
    
}

$htmlContentProcessor = new HtmlContentProcessor(
    new ClassAttributeCollector(),
    new StyleNotationFactory(
        breakpointRegistry: $breakpointRegistry,
        minifiedTokenPool: new StubbedMinifiedTokenPool(),
        classRegistry: $classRegistry
    ),
    new StyleNotationRegistry()
);
$html = '<div class="display:flex width:23:hover@desktop display:none@desktop"></div><div class="width:23"></div><div class="width:23:hover"></div>';
$processedHtml = $htmlContentProcessor->processHtml($html);
$cssOutput = $htmlContentProcessor->exportCss();
echo $processedHtml . "\n\n";
echo $cssOutput;
