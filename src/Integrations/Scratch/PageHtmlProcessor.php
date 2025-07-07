<?php 

namespace Kenjiefx\VentaCSS\Integrations\Scratch;

use Kenjiefx\VentaCSS\Processor\HtmlContentProcessor;
class PageHtmlProcessor {

    public function __construct(
        public readonly HtmlContentProcessor $htmlContentProcessor
    ) {}

    public function processHtml(
        string $pageHtml
    ) {
        $pageHtml = $this->htmlContentProcessor->processHtml($pageHtml);
        return $pageHtml;
    }

    public function getProcessedCss() {
        return $this->htmlContentProcessor->exportCss();
    }
}