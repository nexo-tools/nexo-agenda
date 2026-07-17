<?php

namespace App\Services;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrSvg
{
    /**
     * Inline SVG markup for a QR code pointing at the given URL. Pure PHP,
     * no external requests.
     */
    public function forUrl(string $url, int $size = 220): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size, 1),
            new SvgImageBackEnd
        );

        $svg = (new Writer($renderer))->writeString($url);

        // Strip the XML declaration so it can be embedded inline.
        return (string) preg_replace('/^<\?xml[^>]*\?>\s*/', '', $svg);
    }
}
