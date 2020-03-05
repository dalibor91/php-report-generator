<?php

require dirname(__DIR__) . '/vendor/autoload.php';

//with cache
$fetcher = new ReportGenerator\Fetcher\Http('https://dalibor.me/example/index.html');

print "fetch: '{$fetcher->fetch()}'\n";
print "parse: '{$fetcher->parse()}'\n";
print "cache: '{$fetcher->getConfig('useCache')}'\n";

// without cache
$fetcher = new ReportGenerator\Fetcher\Http('https://dalibor.me/example/index.html', ['useCache' => false]);

print "fetch: '{$fetcher->fetch()}'\n";
print "parse: '{$fetcher->parse()}'\n";
print "cache: '{$fetcher->getConfig('useCache')}'\n";

$fetcher = new ReportGenerator\Fetcher\HttpJson('https://dalibor.me/example/index.json', ['useCache' => true]);

print 'json to json: ' . (json_encode($fetcher->parse())) . "\n";

$fetcher = new ReportGenerator\Fetcher\HttpCSV('https://dalibor.me/example/index.csv', ['useCache' => true]);
$fetcher->setConfigField('csvDelimiter', ';');
print 'csv to json: ' . (json_encode($fetcher->parse())) . "\n";
