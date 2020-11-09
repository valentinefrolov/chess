<?php
$path = __DIR__ . '/../vendor/bin/phpunit';
$scope = isset($argv[1]) ? " --filter {{$argv[1]}}" : '';
exec("$path tests$scope", $output);
echo implode("\n", $output) ."\n";

