<?php 

namespace Kenjiefx\VentaCSS\Integrations\Scratch;

use Kenjiefx\ScratchPHP\App\Events\CSSBuildCompletedEvent;
use Kenjiefx\ScratchPHP\App\Events\HTMLBuildCompletedEvent;
use Kenjiefx\ScratchPHP\App\Events\ListensTo;
use Kenjiefx\ScratchPHP\App\Events\PageBuildCompletedEvent;
use Kenjiefx\ScratchPHP\App\Events\PageBuildStartedEvent;
use Kenjiefx\ScratchPHP\App\Extensions\ExtensionsInterface;
use Kenjiefx\VentaCSS\Variables\RootVariableService;

class VentaScratchExtension implements ExtensionsInterface {

    public function __construct(
        public readonly BeforePageBuildService $beforePageBuildService,
        public readonly PageHtmlProcessor $pageHtmlProcessor,
        public readonly RootVariableService $rootVariableService
    ) {}
    
    #[ListensTo(PageBuildStartedEvent::class)]
    public function beforePageBuild(PageBuildStartedEvent $event): void {
        $this->beforePageBuildService->run($event->getPageModel());
    }

    #[ListensTo(HTMLBuildCompletedEvent::class)]
    public function processHTML(HTMLBuildCompletedEvent $event): void {
        $modifiedHtml = $this->pageHtmlProcessor->processHtml($event->getContent());
        $this->rootVariableService->collect($modifiedHtml);
        $event->updateContent($modifiedHtml);
    }

    #[ListensTo(CSSBuildCompletedEvent::class)]
    public function processCSS(CSSBuildCompletedEvent $event): void {
        $originalCss = $event->getContent();
        $this->rootVariableService->collect($originalCss);
        $processedCss = $this->pageHtmlProcessor->getProcessedCss();
        $rootCssVariables = $this->rootVariableService->createRootCssVariables();
        $event->updateContent($rootCssVariables . "\n" . $originalCss . "\n" . $processedCss);
    }

    #[ListensTo(PageBuildCompletedEvent::class)]
    public function afterPageBuild() {
        $this->rootVariableService->clearUsedVariables();
    }

}