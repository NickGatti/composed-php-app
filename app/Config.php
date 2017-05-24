<?php declare(strict_types=1);

namespace NetAccessory;

use \PDO;
use \LessQL\Database;

function loginController($request, $response) {
    $error = '';
    
    if($request->post->get('username', false)) {
        $db = Config::database();
        
        $username = htmlentities(
            $request->post->get('username', '')
        );
        
        $password = md5(
            $request->post->get('password', 'password')
        );
        
        $user = $db->users()
            ->where('username', $username)
            ->fetch();
            
        if(isset($user) && isset($user->password))     {
            if($user->password === $password) {
                $_SESSION['user'] = $username;
                $_SESSION['token'] = crypt($password, "\$5\${$username}");
                $response->redirect->to('/');
                return $response;
            }
        }
    }
    
    $view = new View('form/index', [
        'title' => 'Login',
        'username' => $request->post->get('username', ''),
        'password' => $request->post->get('password', 'password'),
        'register' => false,
        'button' => 'Log In',
        'error' => $error
    ]);
    $response->content->set($view->render());
    return $response;
}

function createController($request, $response) {
    $error = '';
    
    if ($request->post->get('username', false)) {
        $db = Config::database();
        
        $drow = $db->createRow('details', [
            'email' => htmlentities($request->post->get('email'))
        ]);
        $row = $db->createRow('users', [
            'username' => htmlentities($request->post->get('username')),
            'password' => md5($request->post->get('password')),
            'detail' => $drow
        ]);
        
        try {
            $db->begin();
            $drow->save();
            $row->save();
            $db->commit();
            $response->redirect->to('/');
            return $response;
        } catch(Throwable $e) {
            $error = $e->getMessage();
        }
    }
    
    $view = new View('form/index', [
        'title' => 'Register',
        'username' => $request->post->get('username', ''),
        'password' => $request->post->get('password', 'password'),
        'email' => $request->post->get('email', ''),
        'register' => true,
        'button' => 'Register',
        'error' => $error
    ]);
    $response->content->set($view->render());
    return $response;
}

class Config {
    
    static function database() {
        $pdo = new PDO('mysql:dbname=c9;host=0.0.0.0', 'netaccessory', '');
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
                    
                    $rows = $db->table('animals')->fetchAll();
                    $columns = isset($rows[0]) ? get_object_vars($rows[0]) : [];
                    
                    $view = new View('index', [
                        'title' => "Beautiful HTML",
                        'columns' => $columns,
                        'rows' => $rows
                    ]);
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
                'method'   => 'get',
                'name' => 'logout',
                'path' => '/logout',
                'controller' => function($req, $resp) {
                    session_destroy();
                    session_write_close();
                    session_start();
                    session_regenerate_id();
                    $resp->redirect->to('/');
                    return $resp;
                }
            ],
            [
                'method' => 'get',
                'name' => 'animals',
                'path' => '/animals',
                'controller' => function($a, $b) {
                    if(!$a->headers->get('X-Requested-With', false)) {
                        $view = new View('form/animals', [
                            'title' => 'Create an animal, Home slice!',
                        ]);
                        $b->content->set($view->render());
                        return $b;
                    }
                    
                    $b->content->setType('application/json');
                    $b->content->set(json_encode(['message' => 'hello nick!']));
                    return $b;
                }
            ],
            [
                'method' => 'post',
                'name' => 'animals.create',
                'path' => '/animals',
                'controller' => function($a, $b) {
                    if(!$a->headers->get('X-Requested-With', false)) {
                        $b->content->set('Nick! This is for javascript, Bro!');
                        return $b;
                    }
                    
                    $b->content->setType('application/json');
                    $b->content->set(json_encode([
                        'message' => 'hello nick!',
                        'data' => [
                            'request' => json_encode($a),
                            'response' => json_encode($b)
                        ]
                    ]));
                    return $b;
                }
            ],
            [
                'method' => 'get',
                'name' => 'apinick',
                'path' => '/apinick',
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
