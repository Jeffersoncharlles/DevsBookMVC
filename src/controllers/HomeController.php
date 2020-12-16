<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\LoginHandler;
use src\handlers\PostHandler;

class HomeController extends Controller {

    private $loggedUser;

    public function __construct(){
        $this->loggedUser = LoginHandler::checkLogin();
        /*se de certo ele joga para estanciar  */

        if (LoginHandler::checkLogin() === false) {
            $this->redirect('/login');
        }
    }

    public function index() {
        
        $idUser = $this->loggedUser->id;

        $feed = PostHandler::getHomeFeed($idUser);

        $this->render('home', [
            'loggedUser' => $this->loggedUser,
            'feed' => $feed
        ]);
    }

}