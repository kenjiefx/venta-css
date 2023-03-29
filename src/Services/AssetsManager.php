<?php

declare(strict_types=1);
namespace Kenjiefx\VentaCSS\Services;
use Kenjiefx\VentaCSS\VentaConfig;
use Kenjiefx\VentaCSS\Services\VentaDashboard;
use Kenjiefx\VentaCSS\Factories\VentaConfigFactory;
use Kenjiefx\VentaCSS\Assets\Width;
use Kenjiefx\VentaCSS\Assets\MaxWidth;
use Kenjiefx\VentaCSS\Assets\MinWidth;
use Kenjiefx\VentaCSS\Assets\ItemWidth;
use Kenjiefx\VentaCSS\Assets\SmallWidth;
use Kenjiefx\VentaCSS\Assets\Height;
use Kenjiefx\VentaCSS\Assets\DeviceHeight;
use Kenjiefx\VentaCSS\Assets\MinHeight;
use Kenjiefx\VentaCSS\Assets\Margin;
use Kenjiefx\VentaCSS\Assets\Padding;
use Kenjiefx\VentaCSS\Assets\Text;
use Kenjiefx\VentaCSS\Assets\LineHeight;
use Kenjiefx\VentaCSS\Assets\LetterSpacing;
use Kenjiefx\VentaCSS\Assets\FontWeight;
use Kenjiefx\VentaCSS\Assets\Border;
use Kenjiefx\VentaCSS\Assets\Color;
use Kenjiefx\VentaCSS\Assets\Flexbox;

class AssetsManager {

    private static array $ListOfUtilityClasses;
    private VentaConfig $VentaConfig;

    public function __construct(
        private VentaConfigFactory $VentaConfigFactory,
        private VentaDashboard $VentaDashboard
        )
    {
        $this->VentaConfig = VentaConfigFactory::create();
    }

    public function compileAssets()
    {
        if (!isset(static::$ListOfUtilityClasses)) {
            Width::assign($this,$this->VentaDashboard);
            MaxWidth::assign($this,$this->VentaDashboard);
            MinWidth::assign($this,$this->VentaDashboard);
            ItemWidth::assign($this,$this->VentaDashboard);
            SmallWidth::assign($this,$this->VentaDashboard);
            Height::assign($this,$this->VentaDashboard);
            DeviceHeight::assign($this,$this->VentaDashboard);
            MinHeight::assign($this,$this->VentaDashboard);
            Margin::assign($this,$this->VentaDashboard);
            Padding::assign($this,$this->VentaDashboard);
            Text::assign($this,$this->VentaDashboard);
            LineHeight::assign($this,$this->VentaDashboard);
            LetterSpacing::assign($this,$this->VentaDashboard);
            FontWeight::assign($this,$this->VentaDashboard);
            Border::assign($this,$this->VentaDashboard);
            Color::assign($this,$this->VentaDashboard);
            Flexbox::assign($this,$this->VentaDashboard);
        }
        return static::$ListOfUtilityClasses;
    }

    public function getRaw(
        string $selector
    ){
        return $this->VentaConfig->getSettings($selector);
    }

    public function setRefined(
        string $key,
        string $value
    ){
        static::$ListOfUtilityClasses[$key] = $value;
    }

}
