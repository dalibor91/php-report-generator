<?php

namespace ReportGenerator;

use ReportGenerator\Generator\PDF;

class Factory {
  public static function pdf(string $template, array $data = []) {
    $pdf = new PDF(['template' => $template]);
    $pdf->setData($data);

    return $pdf;
  }

  public static function pdfFromFile(string $file, $data = []) {
    return self::pdf(file_get_contents($file), $data);
  }

  public static function pdfFromSource(Fetcher\Fetcher $fetcher, $data = []) {
    return self::pdf($fetcher->parse(), $data);
  }
}
