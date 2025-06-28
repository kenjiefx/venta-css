<?php 

namespace Kenjiefx\VentaCSS\Integrations\Scratch;

use Kenjiefx\ScratchPHP\App\Events\CSSBuildCompletedEvent;
use Kenjiefx\ScratchPHP\App\Events\HTMLBuildCompletedEvent;
use Kenjiefx\ScratchPHP\App\Events\ListensTo;
use Kenjiefx\ScratchPHP\App\Events\PageBuildStartedEvent;
use Kenjiefx\ScratchPHP\App\Extensions\ExtensionsInterface;

class VentaScratchExtension implements ExtensionsInterface {

    public function __construct(
        public readonly BeforePageBuildService $beforePageBuildService,
        public readonly PageHtmlProcessor $pageHtmlProcessor
    ) {}
    
    #[ListensTo(PageBuildStartedEvent::class)]
    public function beforePageBuild(PageBuildStartedEvent $event): void {
        $this->beforePageBuildService->run();
    }

    #[ListensTo(HTMLBuildCompletedEvent::class)]
    public function processHTML(HTMLBuildCompletedEvent $event): void {
        $modifiedHtml = $this->pageHtmlProcessor->processHtml($event->getContent());
        $event->updateContent($modifiedHtml);
    }

    #[ListensTo(CSSBuildCompletedEvent::class)]
    public function processCSS(CSSBuildCompletedEvent $event): void {
        $originalCss = $event->getContent();
        $processedCss = $this->pageHtmlProcessor->getProcessedCss();
        $event->updateContent($originalCss . "\n" . $processedCss);
    }

}