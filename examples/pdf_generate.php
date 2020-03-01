<?php

require dirname(__DIR__).'/vendor/autoload.php';


$pdf = \ReportGenerator\Factory::pdfFromFile(__DIR__.'/test.html');
$pdf->setMetadata([
  'time' => time()
]);
$pdf->saveTo(['test' => 'foo'], __DIR__.'/test.pdf');