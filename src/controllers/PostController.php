<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use src\handlers\PostHandler;

class PostController extends Controller {

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
    public function new() {
        
        $body = filter_input(INPUT_POST, 'body');
        $user = $this->loggedUser->id;
        
        $type = 'text';

        if ($body) {
            PostHandler::addPost($user, $type, $body);
        }
        $this->redirect('/');
    }
/*===============================================================================*/
/*===============================================================================*/
    public function delete($atr = []) {
        if(!empty($atr['id'])) {
            $idPost = $atr['id'];
            
            PostHandler::delete(
                $idPost, 
                $this->loggedUser->id
            );
        }
        $this->redirect('/');
    }
/*===============================================================================*/
/*===============================================================================*/
}