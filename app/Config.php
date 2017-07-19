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
            
        if(isset($user) && isset($user->password)) {
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
                    $columns = isset($rows[0]) ? array_keys(
                        json_decode(json_encode($rows[0]), true)
                    ) : [];
                    
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
                'method' => 'get',
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
                            'user' => (object) $_SESSION,
                            'id' => '',
                            'name' => '',
                            'color' => '',
                            'image' => '',
                            'description' => '',
                            'wearable' => false,
                            'editing' => 'false'
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
                'method' => 'get',
                'name' => 'animals.edit',
                'path' => '/animals/{animal}',
                'controller' => function($a, $b) {
                    $db = Config::database();
                    
                    $animal = $db->table('animals')
                        ->where('id', $a->attributes->animal)
                        ->fetch();
                    
                    if(!$a->headers->get('X-Requested-With', false)) {
                        $view = new View('form/animals', [
                            'title' => 'Create an animal, Home slice!',
                            'user' => (object) $_SESSION,
                            'id' => $animal->id,
                            'name' => $animal->name,
                            'color' => $animal->color,
                            'image' => $animal->image,
                            'description' => $animal->description,
                            'wearable' => (boolean) $animal->wearable,
                            'editing' => 'true'
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
                    
                    $db = Config::database();
                    $b->content->setType('application/json');
                    
                    $user = $db->table('users')
                        ->where('username', $a->post->get('username'))
                        ->fetch();
                        
                    if(!$user) {
                        // Error
                        $b->content->set(json_encode([
                            'message' => "Failed. {$a->post->get('username')} is not a valid user.",
                            'code' => '404'
                        ]));
                        $b->status->setCode('404');
                        return $b;
                    }
                    
                    if(crypt($user->password, "\$5\${$user->username}") !== $a->post->get('token')) {
                        $b->content->set(json_encode([
                            'message' => "Not authorized, Yo!",
                            'code' => '403'
                        ]));
                        $b->status->setCode('403');
                        return $b;
                    }
                    
                    try {
                        $row = $db->createRow('animals', [
                            'color' => $a->post->get('color'),
                            'name' => $a->post->get('name'),
                            'description' => $a->post->get('description'),
                            'image' => $a->post->get('image'),
                            'wearable' => (boolean) $a->post->get('wearable'),
                            'user' => $user
                        ]);
                        
                        $db->begin();
                        $row->save();
                        $db->commit();
                    } catch(\Throwable $e) {
                        $b->content->set(json_encode([
                            'message' => $e->getMessage(),
                            'code' => '406'
                        ]));
                        $b->status->setCode('406');
                        
                        return $b;
                    }
                    
                    $b->content->set(json_encode([
                        'message' => 'success',
                        'data' => [
                            'request' => json_encode($a),
                            'response' => json_encode($b),
                            'post' => json_encode($a->post->get())
                        ]
                    ]));
                    return $b;
                }
            ],
            [
                'method' => 'put',
                'name' => 'animals.update',
                'path' => '/animals',
                'controller' => function($a, $b) {
                    if(!$a->headers->get('X-Requested-With', false)) {
                        $b->content->set('Nick! This is for javascript, Bro!');
                        return $b;
                    }
                    
                    $db = Config::database();
                    $b->content->setType('application/json');
                    
                    
                    
                    $user = $db->table('users')
                        ->where('username', $a->post->get('username'))
                        ->fetch();
                        
                    if(!$user) {
                        // Error
                        $b->content->set(json_encode([
                            'message' => "Failed. {$a->post->get('username')} is not a valid user.",
                            'code' => '404'
                        ]));
                        $b->status->setCode('404');
                        return $b;
                    }
                    
                    if(crypt($user->password, "\$5\${$user->username}") !== $a->post->get('token')) {
                        $b->content->set(json_encode([
                            'message' => "Not authorized, Yo!",
                            'code' => '403'
                        ]));
                        $b->status->setCode('403');
                        return $b;
                    }
                    
                    try {
                        $row = $db->table('animals')
                            ->where('id', $a->post->get('id'))
                            ->fetch();
                        
                        $row->color = $a->post->get('color');
                        $row->name = $a->post->get('name');
                        $row->description = $a->post->get('description');
                        $row->image = $a->post->get('image');
                        $row->wearable = (boolean) $a->post->get('wearable');
                        
                        $db->begin();
                        $row->save();
                        $db->commit();
                    } catch(\Throwable $e) {
                        $b->content->set(json_encode([
                            'message' => $e->getMessage(),
                            'code' => '406'
                        ]));
                        $b->status->setCode('406');
                        
                        return $b;
                    }
                    
                    $b->content->set(json_encode([
                        'message' => 'success',
                        'data' => [
                            'request' => json_encode($a),
                            'response' => json_encode($b),
                            'post' => json_encode($a->post->get())
                        ]
                    ]));
                    return $b;
                }
            ]
        ];
    }
}
