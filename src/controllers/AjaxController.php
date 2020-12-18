<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use src\handlers\PostHandler;

class AjaxController extends Controller {

    private $loggedUser;

    public function __construct(){
        $this->loggedUser = UserHandler::checkLogin();
        /*se de certo ele joga para estanciar  */

        if (UserHandler::checkLogin() === false) {
            header('Content-type: application/json');
            echo json_encode(['error'=> 'Usuario nao logado']);
            exit;
        }
    }
/*===============================================================================*/
/*===============================================================================*/
    public function like($likes){
       $id = $likes['id'];

       if (PostHandler::isLiked($id,$this->loggedUser->id)) {
         //delete no like
         PostHandler::deleteLike($id,$this->loggedUser->id);
       }else{
           //inserir o like
           PostHandler::addLike($id,$this->loggedUser->id);
       }
    }
/*===============================================================================*/
/*===============================================================================*/
}