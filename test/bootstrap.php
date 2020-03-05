<?php

require dirname(__DIR__) . '/vendor/autoload.php';

function mockedClass(string $name): Mockery\MockInterface {
  return Mockery::mock($name)->shouldAllowMockingProtectedMethods();
}

function getMethod(string $clasz, string $method): ReflectionMethod {
  $obj = new ReflectionMethod($clasz, $method);
  $obj->setAccessible(true);

  return $obj;
}

function getProperty(string $clasz, string $property): ReflectionProperty {
  $obj = new ReflectionProperty($clasz, $property);
  $obj->setAccessible(true);

  return $obj;
}
