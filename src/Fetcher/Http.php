<?php

namespace ReportGenerator\Fetcher;

use \Curl\Curl;

class Http extends Fetcher {
  /**
   * @var Curl
   */
  protected $curl;

  protected $defaultConfig = [
    'followLocation' => true,
    'useCache' => true,
  ];

  protected $cache = [];

  public function init() {
    $this->getLogger()->info('Init CURL connection');
    $this->curl = new Curl();
    $this->applyConfig();
  }

  protected function checkError() {
    if ($this->getCurl()->error) {
      $this->getLogger()->error($this->getCurl()->errorMessage, [
        'source' => $this->source,
        'error' => $this->getCurl()->getCurlErrorMessage(),
        'errorCode' => $this->getCurl()->getErrorCode(),
        'httpErrorMessage' => $this->getCurl()->getHttpErrorMessage(),
      ]);

      throw new Exception("Unable to fetch: {$this->getCurl()->getCurlErrorMessage()}", Exception::HTTP_EXCEPTION);
    }

    return $this->getCurl();
  }

  /**
   * @return Curl
   */
  protected function getCurl() {
    return $this->curl;
  }

  protected function hasCache(string $name) {
    return array_key_exists($name, $this->cache);
  }

  protected function getCache(string $name, $default = null) {
    return $this->cache[$name] ?? $default;
  }

  protected function setCache(string $name, $value) {
    $this->cache[$name] = $value;

    return $this->getCache($name);
  }

  protected function get() {
    // check cache first
    $cacheKey = "GET_{$this->source}";
    if ($this->getConfig('useCache') && $this->hasCache($cacheKey)) {
      $this->getLogger()->info('Cache hit');

      return $this->getCache($cacheKey);
    }

    $this->getLogger()->info('GET Request', [
      'source' => $this->source,
      'config' => $this->config,
    ]);
    $this->getCurl()->get($this->getSource());

    return $this->getConfig('useCache')
      ? $this->setCache($cacheKey, $this->checkError()->response)
      : $this->checkError()->response;
  }

  public function fetch() {
    return $this->get();
  }

  public function parse() {
    $data = $this->fetch();

    return is_string($data) ? trim($data) : $data;
  }

  public function finish() {
    $this->getLogger()->info('Closing CURL connection');
    $this->getCurl()->close();
  }

  protected function applyConfig() {
    $this->curl->setOpt(CURLOPT_FOLLOWLOCATION, $this->getConfig('followLocation', true));

    return $this;
  }
}
