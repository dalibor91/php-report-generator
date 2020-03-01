<?php

namespace ReportGenerator\Fetcher;

class HttpCSV extends Http {
  public function parse() {
    $columns = [];
    $data = [];
    foreach (explode($this->getRowDelimiter(), self::fetch()) as $row) {
      $row = trim($row);

      if (empty($row)) {
        continue;
      }

      $parsed = str_getcsv($row, $this->getDelimiter(), $this->getEnclosure(), $this->getEscape());

      if (empty($columns)) {
        $columns = $parsed;
      } else {
        $tmpData = [];
        foreach ($columns as $key => $value) {
          $tmpData[$value] = $parsed[$key] ?? null;
        }

        $data[] = $tmpData;
      }
    }

    return $data;
  }

  public function getDelimiter(): string {
    return $this->getConfig('csvDelimiter', ',');
  }

  public function getEnclosure(): string {
    return $this->getConfig('csvEnclosure', '"');
  }

  public function getEscape(): string {
    return $this->getConfig('csvEscape', '\\');
  }

  public function getRowDelimiter(): string {
    return $this->getConfig('csvRowDelimiter', "\n");
  }
}