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
        
        $dateFrom = new \DateTime($user->birthdate);
        $dateTo = new \DateTime('today');
        $user->ageYears =$dateFrom->diff($dateTo)->y;
        /*compara hoje menos a data que o cara nasceu e ve quantos anos da isso*/

        $feed = PostHandler::getUserFedd($id);
        
        $this->render('profile', [
            'loggedUser' => $this->loggedUser,
            'user'=> $user,
            'feed'=> $feed
        ]);
    }
}