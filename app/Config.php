<?php declare(strict_types=1);

namespace NetAccessory;

use \PDO;
use \LessQL\Database;

function loginController($request, $response) {
    $view = new View('form/index', [
        'title' => 'Login',
        'username' => $request->post->get('username', ''),
        'password' => $request->post->get('password', 'password'),
        'register' => false,
        'button' => 'Log In'
    ]);
    $response->content->set($view->render());
    return $response;
}

function createController($request, $response) {
    if($request->post->get('username', false)) {
        $db = Config::database();
        
        $drow = $db->createRow('details', [
            'email' => htmlentities($request->post->get('email'))
        ]);
        
        $row = $db->createRow('users', [
            'username' => htmlentities($request->post->get('username')),
            'password' => md5($request->post->get('password')),
            'detail' => $drow
            ]);
            
        $db->begin();
        $drow->save();
        $row->save();
        $db->commit();
    }
    
    
    $view = new View('form/index', [
        'title' => 'Register',
        'username' => $request->post->get('username', ''),
        'password' => $request->post->get('password', 'password'),
        'email' => $request->post->get('email', ''),
        'register' => true,
        'button' => 'Register'
    ]);
    $response->content->set($view->render());
    return $response;
}

class Config {
    
    static function database() {
        $pdo = new PDO('mysql:dbname=c9;host=0.0.0.0', 'nickgatti', '');
        $db = new Database($pdo);
        return $db;
    }
    
    static function routes() {
        return [
            [
                'method' => 'get',
                'name' => 'home',
                'path' => '/',
                'controller' => function($request, $response) {
                    $db = Config::database();
                    
                    $user = false;
                    
                    if (isset($_SESSION['user'])) {
                        $user = $db->table('users')
                            ->where('username', $_SESSION['user'])
                            ->fetch();
                    }
                        
                    if(!$user) {
                        $response->redirect->to('/login');
                        return $response;
                    }
                    
                    $view = new View('index', ['title' => "Beautiful HTML"]);
                    $response->content->set($view->render());
                    return $response;
                }
            ],
            [
                'method' => 'get',
                'name' => 'login',
                'path' => '/login',
                'controller' => 'NetAccessory\loginController'
            ],
            [
                'method' => 'post',
                'name' => 'login.login',
                'path' => '/login',
                'controller' => 'NetAccessory\loginController'
            ],
            [
                'method' => 'get',
                'name' => 'create',
                'path' => '/create',
                'controller' => 'NetAccessory\createController'
            ],
            [
                'method' => 'post',
                'name' => 'create.create',
                'path' => '/create',
                'controller' => 'NetAccessory\createController'
            ],
            [
                'method' => 'get',
                'name' => 'nick',
                'path' => '/nick',
                'controller' => function($a, $b) {
                    if(!$a->headers->get('X-Requested-With', false)) {
                        $b->content->set('Nick! This is for javascript, Bro!');
                        return $b;
                    }
                    $b->content->setType('application/json');
                    $b->content->set(json_encode(['message' => 'hello nick!']));
                    return $b;
                }
            ]
        ];
    }
}
