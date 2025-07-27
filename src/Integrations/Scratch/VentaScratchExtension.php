<?php 

namespace Kenjiefx\VentaCSS\Integrations\Scratch;


use Kenjiefx\ScratchPHP\App\Events\Instances\PageAfterBuildEvent;
use Kenjiefx\ScratchPHP\App\Events\Instances\PageBeforeBuildEvent;
use Kenjiefx\ScratchPHP\App\Events\Instances\PageCSSBuildCompleteEvent;
use Kenjiefx\ScratchPHP\App\Events\Instances\PageHTMLBuildCompleteEvent;
use Kenjiefx\ScratchPHP\App\Events\ListensTo;
use Kenjiefx\ScratchPHP\App\Extensions\ExtensionInterface;
use Kenjiefx\VentaCSS\Variables\RootVariableService;

class VentaScratchExtension implements ExtensionInterface {

    public function __construct(
        public readonly BeforePageBuildService $beforePageBuildService,
        public readonly PageHtmlProcessor $pageHtmlProcessor,
        public readonly RootVariableService $rootVariableService
    ) {}
    
    #[ListensTo(PageBeforeBuildEvent::class)]
    public function beforePageBuild(PageBeforeBuildEvent $event): void {
        $this->beforePageBuildService->run($event->page);
    }

    #[ListensTo(PageHTMLBuildCompleteEvent::class)]
    public function processHTML(PageHTMLBuildCompleteEvent $event): void {
        $modifiedHtml = $this->pageHtmlProcessor->processHtml($event->content);
        $this->rootVariableService->collect($modifiedHtml);
        $event->content = $modifiedHtml;
    }

    #[ListensTo(PageCSSBuildCompleteEvent::class)]
    public function processCSS(PageCSSBuildCompleteEvent $event): void {
        $originalCss = $event->content;
        $this->rootVariableService->collect($originalCss);
        $processedCss = $this->pageHtmlProcessor->getProcessedCss();
        $rootCssVariables = $this->rootVariableService->createRootCssVariables();
        $event->content = $rootCssVariables . "\n" . $originalCss . "\n" . $processedCss;
    }

    #[ListensTo(PageAfterBuildEvent::class)]
    public function afterPageBuild() {
        $this->rootVariableService->clearUsedVariables();
    }

}