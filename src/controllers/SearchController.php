<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use src\handlers\PostHandler;

class SearchController extends Controller {

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
    public function index($sear = []) {
        $searchTerm = filter_input(INPUT_GET, 's');
        if (empty($searchTerm)) {
            $this->redirect('/');
        }   

        $users = UserHandler::searchUser($searchTerm);

        $this->render('search', [
            'loggedUser' => $this->loggedUser,
            'searchTerm'=> $searchTerm,
            'users' => $users
            
        ]);
    }
/*===============================================================================*/
/*===============================================================================*/

}