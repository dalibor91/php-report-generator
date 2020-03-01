<?php

namespace ReportGenerator\Fetcher;

class HttpJson extends Http {
  public function parse() {
    $parsed = parent::parse();
    if ($parsed instanceof \stdClass) {
      return (array)$parsed;
    }

    $data = json_decode(parent::parse(), true);
    if (!$data) {
      $this->getLogger()->error("JSON parsing failed", [
        'error_code' => json_last_error(),
        'error_message' => json_last_error_msg()
      ]);
    }

    return $data;
  }
}