<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use src\handlers\PostHandler;

class HomeController extends Controller {

    private $loggedUser;

    public function __construct(){
        $this->loggedUser = UserHandler::checkLogin();
        /*se de certo ele joga para estanciar  */

        if (UserHandler::checkLogin() === false) {
            $this->redirect('/login');
        }
    }
/*===============================================================================*/
/*===============================================================================*/
    public function index() {
        $page = intval(filter_input(INPUT_GET, 'page'));
        $idUser = $this->loggedUser->id;

        $feed = PostHandler::getHomeFeed($idUser,$page);

        $this->render('home', [
            'loggedUser' => $this->loggedUser,
            'feed' => $feed
        ]);
    }
/*===============================================================================*/
/*===============================================================================*/
    /*public function postsJson(){
        $start = filter_input(INPUT_POST, 'start');
        $limit = filter_input(INPUT_POST, 'limit');
    
        $idUser = $this->loggedUser->id;

        $feedJson = PostHandler::getHomeFeed($idUser,$start,$limit);

        if (count($feedJson) > 0) {
           
            $resposta['code'] = 0;
            $resposta['msg'] = 'tenho resultados';
            $resposta['feed'] = $feedJson;
            echo json_encode($resposta);
            exit;
        } else {
            $resposta['code'] = 1;
            $resposta['msg'] = 'sem resultados';
            $resposta['feed'] = $feedJson;
            echo json_encode($resposta);
            exit;
        }

    }
    */
/*===============================================================================*/
/*===============================================================================*/
}