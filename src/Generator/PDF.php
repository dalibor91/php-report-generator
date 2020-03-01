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
    $this->pdf = new Mpdf();
  }

  public function finish() {
    $this->tplEngine = null;
    $this->pdf->cleanup();
    $this->pdf = null;
  }

  public function saveTo($data, string $name) {
    $this->pdf->WriteHTML($this->generate($data));
    return $this->pdf->Output($name,'F');
  }

  public function generate($data) {
    $this->validate();
    $pdf = new \Mustache_Engine();
    return $pdf->render($this->getTemplate(), [
      'meta' => $this->getConfig('metaData'),
      'data' => $data
    ]);
  }

  public function setTemplate(string $template) {
    return $this->setConfigField('template', $template);
  }

  public function setMetadata($data) {
    return $this->setConfigField('metaData', $data);
  }

  private function getTemplate(): ?string {
    return $this->getConfig('template');
  }

  private function validate() {
    if (!$this->getTemplate()) {
      throw new Exception("Unable to fetch template", Exception::GENERATOR_TEMPLATE_MISSING);
    }
  }
}