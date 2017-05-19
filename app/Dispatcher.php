<?php declare(strict_types=1);

namespace NetAccessory;

use Zend\Diactoros\ServerRequestFactory;

class Dispatcher {
    
    public function __construct($router, $routes) {
        $this->router = $router;
        $this->request = ServerRequestFactory::fromGlobals(
            $_SERVER,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES
        );
        $this->prepareRoutes($routes);
    }
    
    protected function prepareRoutes(array $routes) {
        $map = $this->router->getMap();
        foreach($routes as $route) {
            $map->{$route['method']}(
                $route['name'],
                $route['path'],
                $route['controller']
            );
        }
    }
    
    public function dispatch($request, $response) {
        $matcher = $this->router->getMatcher();
        $route = $matcher->match($this->request);
        
        if (! $route) {
            // get the first of the best-available non-matched routes
            $failedRoute = $matcher->getFailedRoute();
        
            // which matching rule failed?
            switch ($failedRoute->failedRule) {
                case 'Aura\Router\Rule\Allows':
                    // 405 METHOD NOT ALLOWED
                    // Send the $failedRoute->allows as 'Allow:'
                    $response->content->set("Not Allowed - Dude");
                    $response->status->setCode(405);
                    break;
                case 'Aura\Router\Rule\Accepts':
                    // 406 NOT ACCEPTABLE
                    $response->content->set("Not Acceptable - Dude");
                    $response->status->setCode(406);
                    break;
                default:
                    // 404
                    $response->content->set("Not Found - Dude");
                    $response->status->setCode(404);
                    break;
            }
        } else {
            $request->attributes = (object)$route->attributes;
            $callable = $route->handler;
            $response = $callable($request, $response);
        }
        
        return $response;
    }
    
}
