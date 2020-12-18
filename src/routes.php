<?php
use core\Router;

$router = new Router();

/*rotas estaticas get*/
$router->get('/', 'HomeController@index');
$router->get('/login', 'LoginController@signin');
$router->get('/cadastro', 'LoginController@signup');
$router->get('/sair', 'LoginController@logout');

//$router->get('/pesquisa', '@');
$router->get('/perfil/{id}', 'ProfileController@index');
$router->get('/perfil', 'ProfileController@index');

//$router->get('/amigos', '@');
//$router->get('/fotos', '@');
//$router->get('/config', '@');
//$router->get('/sair', '@');


/*rotas estaticas POST*/
$router->post('/login', 'LoginController@signinAction');
$router->post('/cadastro', 'LoginController@signupAction');
$router->post('/post/new', 'PostController@new');
//$router->post('/posts/json', 'HomeController@postsJson');


