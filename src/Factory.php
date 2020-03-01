<?php

namespace ReportGenerator;

use ReportGenerator\Generator\PDF;

class Factory {
  public static function pdf(string $template) {
    return new PDF(['template' => $template]);
  }

  public static function pdfFromFile(string $file) {
    return self::pdf(file_get_contents($file));
  }
}