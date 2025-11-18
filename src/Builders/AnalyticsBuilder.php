<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Builders;

final class AnalyticsBuilder
{
    public function __construct(
        private array $config = []
    ) {
    }

    public function build(): string
    {
        $analyticsConfig = $this->config['analytics'] ?? [];
        $html = '';

        // Google Analytics 4
        if (!empty($analyticsConfig['ga4']['measurement_id'])) {
            $measurementId = $analyticsConfig['ga4']['measurement_id'];
            $html .= '<!-- Google tag (gtag.js) -->' . PHP_EOL;
            $html .= '<script async src="https://www.googletagmanager.com/gtag/js?id=' . $measurementId . '"></script>' . PHP_EOL;
            $html .= '<script>' . PHP_EOL;
            $html .= '  window.dataLayer = window.dataLayer || [];' . PHP_EOL;
            $html .= '  function gtag(){dataLayer.push(arguments);}' . PHP_EOL;
            $html .= '  gtag(\'js\', new Date());' . PHP_EOL;
            $html .= '  gtag(\'config\', \'' . $measurementId . '\');' . PHP_EOL;
            $html .= '</script>' . PHP_EOL;
        }

        // Google Tag Manager
        if (!empty($analyticsConfig['gtm']['container_id'])) {
            $containerId = $analyticsConfig['gtm']['container_id'];
            $html .= '<!-- Google Tag Manager -->' . PHP_EOL;
            $html .= '<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({\'gtm.start\':' . PHP_EOL;
            $html .= 'new Date().getTime(),event:\'gtm.js\'});var f=d.getElementsByTagName(s)[0],' . PHP_EOL;
            $html .= 'j=d.createElement(s),dl=l!=\'dataLayer\'?\'&l=\'+l:\'\';j.async=true;j.src=' . PHP_EOL;
            $html .= '\'https://www.googletagmanager.com/gtm.js?id=\'+i+dl;f.parentNode.insertBefore(j,f);' . PHP_EOL;
            $html .= '})(window,document,\'script\',\'dataLayer\',\'' . $containerId . '\');</script>' . PHP_EOL;
            $html .= '<!-- End Google Tag Manager -->' . PHP_EOL;
        }

        // Yandex Metrica
        if (!empty($analyticsConfig['yandex']['counter_id'])) {
            $counterId = $analyticsConfig['yandex']['counter_id'];
            $html .= '<!-- Yandex.Metrika counter -->' . PHP_EOL;
            $html .= '<script type="text/javascript">' . PHP_EOL;
            $html .= '   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};' . PHP_EOL;
            $html .= '   m[i].l=1*new Date();' . PHP_EOL;
            $html .= '   for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}' . PHP_EOL;
            $html .= '   k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})' . PHP_EOL;
            $html .= '   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");' . PHP_EOL;
            $html .= '   ym(' . $counterId . ', "init", {' . PHP_EOL;
            $html .= '        clickmap:true,' . PHP_EOL;
            $html .= '        trackLinks:true,' . PHP_EOL;
            $html .= '        accurateTrackBounce:true' . PHP_EOL;
            $html .= '   });' . PHP_EOL;
            $html .= '</script>' . PHP_EOL;
            $html .= '<noscript><div><img src="https://mc.yandex.ru/watch/' . $counterId . '" style="position:absolute; left:-9999px;" alt="" /></div></noscript>' . PHP_EOL;
            $html .= '<!-- /Yandex.Metrika counter -->' . PHP_EOL;
        }

        // Facebook Pixel
        if (!empty($analyticsConfig['facebook']['pixel_id'])) {
            $pixelId = $analyticsConfig['facebook']['pixel_id'];
            $html .= '<!-- Facebook Pixel Code -->' . PHP_EOL;
            $html .= '<script>' . PHP_EOL;
            $html .= '!function(f,b,e,v,n,t,s)' . PHP_EOL;
            $html .= '{if(f.fbq)return;n=f.fbq=function(){n.callMethod?' . PHP_EOL;
            $html .= 'n.callMethod.apply(n,arguments):n.queue.push(arguments)};' . PHP_EOL;
            $html .= 'if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version=\'2.0\';' . PHP_EOL;
            $html .= 'n.queue=[];t=b.createElement(e);t.async=!0;' . PHP_EOL;
            $html .= 't.src=v;s=b.getElementsByTagName(e)[0];' . PHP_EOL;
            $html .= 's.parentNode.insertBefore(t,s)}(window, document,\'script\',' . PHP_EOL;
            $html .= '\'https://connect.facebook.net/en_US/fbevents.js\');' . PHP_EOL;
            $html .= 'fbq(\'init\', \'' . $pixelId . '\');' . PHP_EOL;
            $html .= 'fbq(\'track\', \'PageView\');' . PHP_EOL;
            $html .= '</script>' . PHP_EOL;
            $html .= '<noscript><img height="1" width="1" style="display:none"' . PHP_EOL;
            $html .= 'src="https://www.facebook.com/tr?id=' . $pixelId . '&ev=PageView&noscript=1"' . PHP_EOL;
            $html .= '/></noscript>' . PHP_EOL;
            $html .= '<!-- End Facebook Pixel Code -->' . PHP_EOL;
        }

        return $html;
    }
}

