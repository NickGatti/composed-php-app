<?php declare(strict_types=1);

include realpath(__DIR__ . '/../vendor/autoload.php');
error_reporting(E_ALL);

set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        // This error code is not included in error_reporting
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

$environment = 'development';

$Whoops =  new Whoops\Run;

if($environment !== 'production') {
    $Whoops->pushHandler(new Whoops\Handler\PrettyPageHandler);
    $Whoops->pushHandler(new Whoops\Handler\JsonResponseHandler);
} else {
    $Whoops->pushHandler(function($e) {
        echo "TODO: Friendly error page, and email to Developer.";
    });
}

$Whoops->register();

$app = new NetAccessory\Application;
$app->run();
