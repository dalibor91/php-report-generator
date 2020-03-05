<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$pdf = \ReportGenerator\Factory::pdfFromFile(__DIR__ . '/test.html');
$pdf->setMetadata([
  'time' => time(),
]);
$pdf->setData(['test' => 'foo']);
$pdf->saveTo(__DIR__ . '/test.pdf');

$pdf = \ReportGenerator\Factory::pdfFromFile(__DIR__ . '/test1.html');
$pdf->setMetadata([
  'time' => time(),
]);
$pdf->setData(['test' => 'foo']);
$pdf->saveTo(__DIR__ . '/test1.pdf');
