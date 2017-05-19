<?php declare(strict_types=1);

namespace NetAccessory;

use Aura\Web\WebFactory;
use Aura\Router\RouterContainer;
use Zend\Diactoros\ServerRequestFactory;

class Application {
    
    public function __construct() {
        $web_factory = new WebFactory([
            '_ENV' => $_ENV,
            '_GET' => $_GET,
            '_POST' => $_POST,
            '_COOKIE' => $_COOKIE,
            '_SERVER' => $_SERVER
        ]);
        $this->request = $web_factory->newRequest();
        $this->response = $web_factory->newResponse();
        $this->router = new RouterContainer;
        $this->dispatcher = new Dispatcher($this->router, Config::routes());
    }
    
    public function run() {
        session_start();
        $this->sendResponse(
            $this->dispatcher->dispatch(
                $this->request,
                $this->response
            )
        );
        session_write_close();
    }
    
    public function sendResponse($response) {
        // send status line
        header($response->status->get(), true, $response->status->getCode());
        
        // send non-cookie headers
        foreach ($response->headers->get() as $label => $value) {
            if (is_array($value)) {
                foreach ($value as $val) {
                    header("{$label}: {$val}", false);
                }
            } else {
                header("{$label}: {$value}");
            }
        }
        
        // send cookies
        foreach ($response->cookies->get() as $name => $cookie) {
            setcookie(
                $name,
                $cookie['value'],
                $cookie['expire'],
                $cookie['path'],
                $cookie['domain'],
                $cookie['secure'],
                $cookie['httponly']
            );
        }
        
        // send content
        echo $response->content->get();
    }
    
}
