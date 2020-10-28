<?php
$path = __DIR__ . '/../vendor/bin/phpunit';
exec("$path tests", $output);
echo implode("\n", $output) ."\n";

