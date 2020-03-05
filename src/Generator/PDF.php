<?php

namespace ReportGenerator\Generator;

use Mpdf\Mpdf;

class PDF extends Generator {
  /**
   * @var \Mustache_Engine
   */
  protected $tplEngine;

  /**
   * @var Mpdf
   */
  protected $pdf;

  public function init() {
    $this->tplEngine = new \Mustache_Engine();
    $this->pdf = new Mpdf($this->getConfig('pageSettings', []));
  }

  public function finish() {
    $this->tplEngine = null;
    $this->pdf->cleanup();
    $this->pdf = null;
  }

  public function saveTo(string $name) {
    $this->getPdf()->WriteHTML($this->generate());

    return $this->getPdf()->Output($name, 'F');
  }

  public function generate() {
    $this->validate();
    $pdf = new \Mustache_Engine();

    return $pdf->render($this->getTemplate(), [
      'meta' => $this->getConfig('metaData'),
      'data' => $this->getData(),
    ]);
  }

  public function setTemplate(string $template) {
    return $this->setConfigField('template', $template);
  }

  public function setMetadata(array $data) {
    return $this->setConfigField('metaData', $data);
  }

  public function setPageSettings(array $data) {
    return $this->setConfigField('pageSettings', $data);
  }

  private function getTemplate(): ?string {
    return $this->getConfig('template');
  }

  public function getPdf(): Mpdf {
    return $this->pdf;
  }

  private function validate() {
    if (!$this->getTemplate()) {
      throw new Exception('Unable to fetch template', Exception::GENERATOR_TEMPLATE_MISSING);
    }
  }
}
