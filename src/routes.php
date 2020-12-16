<?php
use core\Router;

$router = new Router();

/*rotas estaticas get*/
$router->get('/', 'HomeController@index');
$router->get('/login', 'LoginController@signin');
$router->get('/cadastro', 'LoginController@signup');


/*rotas estaticas POST*/
$router->post('/login', 'LoginController@signinAction');
$router->post('/cadastro', 'LoginController@signupAction');