<?php

namespace ReportGenerator\Fetcher;

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Mockery;

class HttpTest extends TestCase {
  /**
   * @var Logger
   */
  protected $logger;

  /**
   * @var Mockery\MockInterface|Mockery\LegacyMockInterface
   */
  protected $mock;

  public function setUp(): void {
    $this->logger = new Logger('test_logger');
    $this->logger->pushHandler(new TestHandler());

    $this->mock = mockedClass(Http::class);
  }

  protected function log($index) {
    return $this->logger->getHandlers()[0]->getRecords()[$index];
  }

  protected function logMessage($index) {
    return $this->log($index)['message'];
  }

  public function testInit() {
    $this->mock->shouldReceive('applyConfig')->once();
    $this->mock->shouldReceive('getLogger')->andReturn($this->logger);
    $this->mock->mockery_callSubjectMethod('init', []);

    $this->assertSame('Init CURL connection', $this->logMessage(0));
  }

  public function testCacheWorks() {
    $http = new Http('test');

    $this->assertEmpty(getMethod(Http::class, 'getCache')->invoke($http, 'testCache'));
    $this->assertFalse(getMethod(Http::class, 'hasCache')->invoke($http, 'testCache'));

    getMethod(Http::class, 'setCache')->invoke($http, 'testCache', [12345]);

    $this->assertNotEmpty(getMethod(Http::class, 'getCache')->invoke($http, 'testCache'));
    $this->assertTrue(getMethod(Http::class, 'hasCache')->invoke($http, 'testCache'));
    $this->assertSame([12345], getMethod(Http::class, 'getCache')->invoke($http, 'testCache'));
  }

  public function testCheckErrorWithError() {
    $mockedCurl = mockedClass(Curl::class);
    $mockedCurl->error = 1234;
    $mockedCurl->errorMessage = 1234;
    $mockedCurl->shouldReceive('getHttpErrorMessage')->andReturn('http msg')->once();
    $mockedCurl->shouldReceive('getCurlErrorMessage')->andReturn('msg')->once();
    $mockedCurl->shouldReceive('getErrorCode')->andReturn('1234')->once();
    $this->mock->shouldReceive('getLogger')->andReturn($this->logger);

    $this->mock->shouldReceive('getCurl')->andReturn($mockedCurl);

    try {
      $this->mock->mockery_callSubjectMethod('checkError', []);

      throw new \Exception('unreachable ');
    } catch (Exception $e) {
      $this->assertSame('Unable to fetch: msg', $e->getMessage());
      $this->assertSame(Exception::HTTP_EXCEPTION, $e->getCode());
    }

    $this->assertSame('1234', $this->logMessage(0));
  }

  public function testGetData() {
    $mockedCurl = mockedClass(Curl::class);
    $mockedCurl->response = '1234';
    $this->mock->shouldReceive('getSource')->once()->andReturn('http://test');
    $mockedCurl->shouldReceive('get')->once()->with('http://test');

    $this->mock->shouldReceive('getCurl')->andReturn($mockedCurl);
    $this->mock->shouldReceive('getConfig')->andReturn([]);
    $this->mock->shouldReceive('getLogger')->andReturn($this->logger);
    $this->mock->shouldReceive('checkError')->once()->andReturn($mockedCurl);

    $this->assertSame('1234', $this->mock->mockery_callSubjectMethod('get', []));
  }

  public function testParseData() {
    $this->mock->shouldReceive('fetch')->once()->andReturn(' 1 2 3 ');
    $this->assertSame('1 2 3', $this->mock->mockery_callSubjectMethod('parse', []));
  }

  public function testParseDataNotString() {
    $this->mock->shouldReceive('fetch')->once()->andReturn([1,2,3]);
    $this->assertSame([1,2,3], $this->mock->mockery_callSubjectMethod('parse', []));
  }
}
