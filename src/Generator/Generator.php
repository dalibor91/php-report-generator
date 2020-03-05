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

  protected $data;

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

  final public function setData($data) {
    $this->data = $data;

    return $this;
  }

  final public function getData() {
    return $this->data;
  }

  abstract public function generate();

  abstract public function saveTo(string $name);

  abstract public function init();

  abstract public function finish();
}
