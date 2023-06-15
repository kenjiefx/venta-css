<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS;
use Kenjiefx\ScratchPHP\App\Components\ComponentModel;
use Kenjiefx\ScratchPHP\App\Events\ListensTo;
use Kenjiefx\ScratchPHP\App\Events\OnBuildCssEvent;
use Kenjiefx\ScratchPHP\App\Events\OnBuildHtmlEvent;
use Kenjiefx\ScratchPHP\App\Interfaces\ExtensionsInterface;
use Kenjiefx\VentaCSS\Groupings\GroupedUtilityClassCompiler;
use Kenjiefx\VentaCSS\MediaQueries\MediaQueryCompiler;
use Kenjiefx\VentaCSS\PageHtml\PageHtmlMutator;
use Kenjiefx\VentaCSS\Registries\ClassRegistry;
use Kenjiefx\VentaCSS\Utilities\ClassNameMinifierService;
use Kenjiefx\VentaCSS\Utilities\UtilityClassCompiler;
use Kenjiefx\VentaCSS\VentaConfig;
use Kenjiefx\VentaCSS\Factories\VentaConfigFactory;


class VentaCSS implements ExtensionsInterface {


    /**
     * The HTML document or page before Venta CSS processes were completed
     */
    private string $preprocess_html;

    /**
     * The CSS codes of the page Html after Venta CSS processes were completed
     */
    private string $postprocess_css;

    private VentaConfig $VentaConfig;


    public function __construct(
        private VentaConfigFactory $VentaConfigFactory,
        private ClassRegistry $ClassRegistry,
        private GroupedUtilityClassCompiler $GroupedUtilityClassCompiler,
        private UtilityClassCompiler $UtilityClassCompiler,
        private PageHtmlMutator $PageHtmlMutator,
        private MediaQueryCompiler $MediaQueryCompiler,
        private ClassNameMinifierService $ClassNameMinifierService
        )
    {
        $this->VentaConfig = VentaConfigFactory::create();
    }


    #[ListensTo(OnBuildHtmlEvent::class)]
    public function mutatePageHTML(string $page_html): string
    {
        $this->preprocess_html = $page_html;
        $this->run_extension();
        $this->generate_postprocess_css();
        $postprocessed_html = $this->PageHtmlMutator->mutate(($page_html));
        $this->clear_all_registry();
        return $postprocessed_html;
    }


    #[ListensTo(OnBuildCssEvent::class)]
    public function mutatePageCSS(string $page_css): string
    {
        $postprocess_css = $page_css.$this->postprocess_css;
        # Clearing $this->postprocess_css for the next page render
        $this->postprocess_css = '';
        return $postprocess_css;
    }

    public function run_extension()
    {
        $this->VentaConfig->unpack_config_values();
        $this->ClassRegistry->register($this->preprocess_html);
        $this->GroupedUtilityClassCompiler->compile();
        $this->MediaQueryCompiler->compile();
        $this->UtilityClassCompiler->compile();
    }

    private function clear_all_registry(){
        $this->ClassRegistry->clear_registry();
        $this->GroupedUtilityClassCompiler->clear_grouped_utility_class_registry();
        $this->UtilityClassCompiler->clear_utility_class_registry();
        $this->MediaQueryCompiler->clear_utilized_breakpoints_list();
        $this->ClassNameMinifierService->clear_utilized_minified_names();
    }

    public function generate_postprocess_css() {
        $postprocess_css = $this->UtilityClassCompiler->to_exportable_css();
        $postprocess_css .= $this->MediaQueryCompiler->to_exportable_css();
        $this->postprocess_css = str_replace(["\r","\n","    ","\t"],"",$postprocess_css);
    }

}
