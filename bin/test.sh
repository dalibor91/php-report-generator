#!/usr/bin/env bash

PHP_UNIT="./vendor/bin/phpunit"

if ! [ -f $PHP_UNIT ];
then
    echo "phpunit not installed"
    exit 1
fi


$PHP_UNIT --bootstrap test/bootstrap.php test