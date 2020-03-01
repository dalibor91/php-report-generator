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
    if ($this->curl->error) {
      $this->getLogger()->error($this->curl->errorMessage, [
        'source' => $this->source,
        'error' => $this->curl->getCurlErrorMessage(),
        'errorCode' => $this->curl->getErrorCode(),
        'httpErrorMessage' => $this->curl->getHttpErrorMessage()
      ]);

      throw new Exception("Unable to fetch: {$this->curl->getCurlErrorMessage()}", Exception::HTTP_EXCEPTION);
    }

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
      $this->getLogger()->info("Cache hit");
      return $this->getCache($cacheKey);
    }


    $this->getLogger()->info("GET Request", [
      'source' => $this->source,
      'config' => $this->config
    ]);
    $this->curl->get($this->source);

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
    $this->curl->close();
  }

  private function applyConfig() {
    $this->curl->setOpt(CURLOPT_FOLLOWLOCATION, $this->getConfig('followLocation', true));

    return $this;
  }
}