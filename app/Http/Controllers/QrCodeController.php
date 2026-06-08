<?php

namespace App\Http\Controllers;

use App\Models\Album;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QrCodeController extends Controller
{
    public function __invoke(Album $album)
    {
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_MARKUP_SVG,
            'svgAddXmlHeader' => false,
            'drawLightModules' => false,
            'scale' => 5,
        ]);

        $svg = (new QRCode($options))->render($album->shareUrl());

        return response($svg, 200, ['Content-Type' => 'image/svg+xml']);
    }
}
