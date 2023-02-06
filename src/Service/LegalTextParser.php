<?php

namespace App\Service;

use Smalot\PdfParser\Parser;

class LegalTextParser
{
    private Parser $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    public function getParagraphs(string $file): string
    {
        $pdf = $this->parser->parseFile($file);

        $legalText = trim($pdf->getText());

        // Remove spaces
        $legalText = str_replace("\t", '', $legalText);

        // Remove artifacts
        $legalText = str_replace('[Signature]', '', $legalText);
        $legalText = str_replace('[QR Code]', '', $legalText);
        $legalText = str_replace('[QR Code]', '', $legalText);
        $legalText = str_replace('[Signatur]', '', $legalText);

        // Trim each line
        $legalText = preg_replace('/\s*\n\s*/', "\n", $legalText);

        $legalText = preg_replace('/^Vorentwurf(\s*vom)?\s*([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4})?/m', '', $legalText);
        $legalText = preg_replace('/^Änderung vom\s?.*\n/m', '', $legalText);
        $legalText = preg_replace('/^Die Bundesversammlung.+?beschliesst:\n/sm', '', $legalText);
        $legalText = preg_replace('/^Der Schweizerische Bundesrat.+?verordnet:\n/sm', '', $legalText);

        $legalText = preg_replace('/«\s?\$\$e\s*-\s*s\s*e\s*a\s*l\s?»/', '', $legalText);
        $legalText = str_replace('«$$QrCode»', '', $legalText);
        $legalText = str_replace('«%KAVID»', '', $legalText);
        $legalText = preg_replace('/«\s?%\s*A\s*S\s*F\s*F\s*_\s*Y\s*Y\s*Y\s*Y\s*_\s*I\s*D\s?»/', '', $legalText);

        // Silbentrennung (re-join)
        $legalText = preg_replace('/\b-\s*\n\b/', '', $legalText);

        // roman numbers (sometimes immediately followed by a capitalized word or number (list point)
        $legalText = preg_replace('/(\b[IVX]+)(\b|[A-Z0-9])/', "\n\n$1\n$2", $legalText);

        // list points
        $legalText = preg_replace('/\b[a-z]{1,2}\. /', "\n$0\n", $legalText);
        $legalText = preg_replace('/^\d{1,2} (?=[A-Z])/m', "\n$0\n", $legalText);

        // Article titles
        // Sometimes Articles are mentioned inside a sentence or in brackets. These should not be treated as titles.
        $legalText = preg_replace('/(?<=[.;:\sa-z])Art\. \d+/', "\n\n$0", $legalText);

        // remove page numbers like "1 / 3"
        $legalText = preg_replace('%^\d+\s*/\s*\d+$%m', '', $legalText);

        // Remove "YYYY-..." at the beginning of the file
        $legalText = preg_replace('/^\d{4}-\s?.\s*.\s*.\s?$/', '', $legalText);

        // Strip tags, remove unnecessary whitespace at first lines
        $legalText = strip_tags($legalText);
        $legalText = ltrim($legalText);

        return $legalText;
    }
}
