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
        $page = intval(filter_input(INPUT_GET, 'page'));

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

        $feed = PostHandler::getUserFedd($id, $page,$this->loggedUser->id);
        /*No caso são os mesmos pq ele tá acessando o próprio feed, mas o primeiro parametro 
        é de quem é o feed que vc ta puxando 
        e o terceiro é o id de quem ta logado 
        pq o método que pega os posts usa o id do cara logado.*/
        
        $this->render('profile', [
            'loggedUser' => $this->loggedUser,
            'user'=> $user,
            'feed'=> $feed
        ]);
    }
}