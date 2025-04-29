<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

function setValueInCell(string $staticText, $dynamicValue): RichText
{
    $richText = new RichText();
    $boldPart = $richText->createTextRun($staticText);
    $boldPart->getFont()->setBold(true);
    $regularPart = $richText->createTextRun((string)$dynamicValue);

    return $richText;
}
