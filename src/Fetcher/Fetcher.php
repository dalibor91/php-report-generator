<?php

namespace ReportGenerator\Fetcher;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

abstract class Fetcher {
  /**
   * @var string - url from where to fetch the source
   */
  protected $source;

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

  public function __construct(string $source, array $config = []) {
    $this->setSource($source)
        ->setConfig(array_merge($this->defaultConfig, $config))
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
   * @param string $source
   * @return $this
   */
  public function setSource(string $source) {
    $this->source = $source;

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

  protected function getSource(): string {
    return $this->source;
  }

  abstract public function init();

  abstract public function fetch();

  abstract public function parse();

  abstract public function finish();
}
