<?php

namespace ReportGenerator\Fetcher;

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Mockery;

class HttpCSVTest extends TestCase {
  /**
   * @var Logger
   */
  protected $logger;

  /**
   * @var Mockery\MockInterface|Mockery\LegacyMockInterface
   */
  protected $mock;

  protected $csvSettings = [
    'getDelimiter' => ',',
    'getEnclosure' => '"',
    'getEscape' => '\\',
    'getRowDelimiter' => "\n",
  ];

  public function setUp(): void {
    $this->logger = new Logger('test_logger');
    $this->logger->pushHandler(new TestHandler());

    $this->mock = mockedClass(HttpCSV::class);
  }

  protected function log($index) {
    return $this->logger->getHandlers()[0]->getRecords()[$index];
  }

  protected function logMessage($index) {
    return $this->log($index)['message'];
  }

  protected function mockConfig(array $data = []) {
    foreach ($data ?: $this->csvSettings as $method => $return) {
      $this->mock->shouldReceive($method)->andReturn($return);
    }

    return $this->mock;
  }

  protected function createCsv(array $data) {
    $csv = '';
    if ($data) {
      array_unshift($data, array_keys($data[0]));
    }
    foreach ($data as $row => $values) {
      $csv .= (implode($this->csvSettings['getDelimiter'], array_map(function ($item) {
        return
          $this->csvSettings['getEnclosure'] .
          str_replace($this->csvSettings['getEnclosure'], $this->csvSettings['getEscape'] . $this->csvSettings['getEnclosure'], $item) .
          $this->csvSettings['getEnclosure'];
      }, $values)) . $this->csvSettings['getRowDelimiter']);
    }

    return $csv;
  }

  public function testCsv() {
    $this->assertSame('"a","b","c"' . "\n" . '"1","2","3"' . "\n", $this->createCsv([['a' => 1, 'b' => 2, 'c' => 3]]));
    $this->assertSame('"\\"a\\"","b","c"' . "\n" . '"\\"\\"","2","3"' . "\n", $this->createCsv([['"a"' => '""', 'b' => 2, 'c' => 3]]));
  }

  public function testParse() {
    $data = [
      ['foo' => '1', 'bar' => '2', 'baz' => '3'],
      ['foo' => '4', 'bar' => '5', 'baz' => '6'],
    ];
    $this->mock->shouldReceive('fetch')->andReturn($this->createCsv($data));
    $this->mockConfig($this->csvSettings);
    $this->assertSame($data, $this->mock->mockery_callSubjectMethod('parse', []));
  }

  public function testParseWithEscape() {
    $data = [
      ['foo' => '1', 'bar' => '2 1234', 'baz' => '3'],
      ['foo' => '4', 'bar' => '5', 'baz' => '6'],
    ];
    $this->mock->shouldReceive('fetch')->andReturn($this->createCsv($data));
    $this->mockConfig($this->csvSettings);
    $this->assertSame($data, $this->mock->mockery_callSubjectMethod('parse', []));
  }
}
