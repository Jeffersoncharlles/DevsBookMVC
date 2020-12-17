<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use src\handlers\PostHandler;

class ProfileController extends Controller {

    private $loggedUser;

    public function __construct(){
        $this->loggedUser = UserHandler::checkLogin();
        /*se de certo ele joga para estanciar  */

        if (UserHandler::checkLogin() === false) {
            $this->redirect('/login');
        }
    }

    public function index($idUser = []) {

        $id = $this->loggedUser->id;

        if (!empty($idUser['id'])) {
            $id = $idUser['id'];
            //verifica quem ele quer acessar
        }

        $user = UserHandler::getUser($id, true);

        if (!$user) {
            $this->redirect('/');
        }

        
        $this->render('profile', [
            'loggedUser' => $this->loggedUser,
            'user'=> $user
        ]);
    }
}