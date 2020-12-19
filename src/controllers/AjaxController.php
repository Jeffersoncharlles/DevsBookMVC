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
    public function comment(){
        $array = ['error' =>''];

       $id = filter_input(INPUT_POST, 'id');
       $txt = filter_input(INPUT_POST, 'txt');

       if ($id && $txt) {
            PostHandler::addComment($id, $txt,$this->loggedUser->id);

            $array['link'] = '/perfil/'.$this->loggedUser->id;
            $array['avatar'] = '/media/avatars/'.$this->loggedUser->avatar;
            $array['name'] = $this->loggedUser->name;
            $array['body'] = $txt;
       }

       header('Content-type: application/json');
        echo json_encode($array);
        exit;
    }
/*===============================================================================*/
/*===============================================================================*/
    public function upload(){
        $array = ['error' =>''];
        
        $folder = 'media/uploads/';

        if(isset($_FILES['photo']) && !empty($_FILES['photo']['tmp_name'])){
            $photo = $_FILES['photo'];

            $maxWidth = 800;
            $maxHeight = 800;

            if (in_array($photo['type'], ['image/png','image/jpge','image/jpg'])) {

                list($widthOrig, $heightOrig) = getimagesize($photo['tmp_name']);
                $ratio = $widthOrig / $heightOrig ;

                $newwidth = $maxWidth;
                $newheight = $maxHeight;
                $ratioMax = $maxWidth / $maxHeight ;

                if ($ratioMax < $ratio) {
                    $newwidth = $newheight  * $ratio;
                }else{
                    $newheight = $newwidth / $ratio;
                }

                $finalImage = imagecreatetruecolor($newwidth, $newheight);
                switch ($photo['type']) {
                    case 'image/jpeg':
                    case 'image/jpg':
                        $image = imagecreatefromjpeg($photo['tmp_name']);
                        break;
                    case 'image/png':
                        $image = imagecreatefrompng($photo['tmp_name']);
                        break;   
                }

                imagecopyresampled(
                    $finalImage, $image,
                    0, 0 , 0, 0,
                    $newwidth, $newheight, $widthOrig ,$heightOrig
                );

                $photoName = md5(time().rand(0,9999)).'.jpg';
                
                imagejpeg($finalImage, $folder.$photoName);
                PostHandler::addPost($this->loggedUser->id,'photo',$photoName);

            }

        }else{
            $array['error'] = 'Nenhuma imagem enviada!';
        }

        header('Content-type: application/json');
        echo json_encode($array);
        exit;
    }
/*===============================================================================*/
/*===============================================================================*/
}