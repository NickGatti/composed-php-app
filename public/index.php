<?php declare(strict_types=1);

include realpath(__DIR__ . '/../vendor/autoload.php');

echo "Hello World!";

$app = new nickgatti\Application;

$app->run();