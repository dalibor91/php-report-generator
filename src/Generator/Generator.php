<?php

namespace ReportGenerator\Generator;

use Monolog\Logger;

abstract class Generator {
  /**
   * @var array  - configuration
   */
  protected $config = [];

  /**
   * @var array  - default configuration
   */
  protected $defaultConfig = [];

  /**
   * @var Logger
   */
  protected $logger;

  public function __construct(array $config = []) {
    $this->setConfig(array_merge($this->defaultConfig, $config))
      ->init();
  }

  public function __destruct() {
    $this->finish();
  }

  /**
   * @param array $config
   * @return $this
   */
  public function setConfig(array $config) {
    $this->config = $config;
    return $this;
  }

  public function getConfig(string $name, $default = null) {
    return $this->config[$name] ?? $default;
  }

  public function setConfigField(string $name, $value) {
    $this->config[$name] = $value;
    return $this;
  }

  /**
   * @param Logger $logger
   */
  public function setLogger($logger) {
    $this->logger = $logger;
  }

  /**
   * @return Logger
   */
  protected function getLogger() {
    if (!$this->logger) {
      $this->setLogger(new Logger(__NAMESPACE__));
      //$this->logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));
    }

    return $this->logger;
  }

  abstract public function generate($data);

  abstract public function saveTo($data, string $name);

  abstract function init();

  abstract function finish();
}