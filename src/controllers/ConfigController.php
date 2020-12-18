<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use src\handlers\PostHandler;

class ConfigController extends Controller {

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

        $user = UserHandler::getUser($this->loggedUser->id);

        $flash = '';
        if(!empty($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }
        
        $this->render('config', [
            'loggedUser' => $this->loggedUser,
            'user' => $user,
            'flash' => $flash
        ]);
    }
/*===============================================================================*/
/*===============================================================================*/
    public function updateAction() {
        $name = filter_input(INPUT_POST, 'name');
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $birthdate = filter_input(INPUT_POST, 'birthdate');
        $password = filter_input(INPUT_POST, 'password');
        $passwordConfirm = filter_input(INPUT_POST, 'passwordConfirm');
        $city = filter_input(INPUT_POST, 'city');
        $work = filter_input(INPUT_POST, 'work');
        
        
        
        if ($name && $email) {
            $updateFields = [];

            /*==================================*/
            // 1.E-MAIL
            /*==================================*/
            $user = UserHandler::getUser($this->loggedUser->id);
            //E-mail 
                if ($user->email != $email) {
                        if (!UserHandler::emailExists($email)) {
                        $updateFields['email'] = $email;
                    }else{
                        $_SESSION['flash'] = 'E-mail Ja existe!';
                        $this->redirect('/config');
                    }
                }

            
            /*==================================*/
            // 2. BIRTHDATE
            /*==================================*/
            $birthdate = explode('/', $birthdate);
            if(count($birthdate) !=3 ) {
                $_SESSION['flash'] = 'Data de nascimento inválida';
                $this->redirect('/config');
            }

            $birthdate = $birthdate[2].'-'.$birthdate[1].'-'.$birthdate[0];
            if(strtotime($birthdate) === false) {
                $_SESSION['flash'] = 'Data de nascimento inválida';
                $this->redirect('/config');
            }
            $updateFields['birthdate'] = $birthdate;
            
            /*==================================*/
            //3. PASSWORD
            /*==================================*/
            if(!empty($password)) {
                if($password === $passwordConfirm) {
                    $updateFields['password'] = $password;
                }else{
                    $_SESSION['flash'] = 'Senhas Nao Confere';
                    $this->redirect('/config');
                }
            }

            
            /*==================================*/
            //4. CAMPOS NORMAIS
            /*==================================*/
            $updateFields['name'] = $name;
            $updateFields['city'] = $city;
            $updateFields['work'] = $work;

            /*==================================*/
            //5. AVATAR
            /*==================================*/
                if (isset($_FILES['avatar']) && !empty($_FILES['avatar']['tmp_name'])) {
                    $newAvatar = $_FILES['avatar'];

                    if(in_array($newAvatar['type'], ['image/jpeg','image/jpg','image/png'])) {
                        $avatarName = $this->cutImage($newAvatar,200,200,'media/avatars');
                        $updateFields['avatar'] = $avatarName;
                    }
                }

            /*==================================*/
            //6. COVER
            /*==================================*/

            if (isset($_FILES['cover']) && !empty($_FILES['cover']['tmp_name'])) {
                $newCover = $_FILES['cover'];
                if(in_array($newCover['type'], ['image/jpeg','image/jpg','image/png'])) {
                    $coverName = $this->cutImage($newCover,850,310,'media/covers');
                    $updateFields['cover'] = $coverName;
                }
            }

            UserHandler::updateUser($updateFields, $this->loggedUser->id);
       }
        
        $this->redirect('/config');
    }
/*===============================================================================*/
/*=========================FUNCAO AUXILIAR==========================================*/
/*===============================================================================*/
    private function cutImage($file, $la, $aut, $folder){
        list($widthOrig, $heightOrig) = getimagesize($file['tmp_name']);
        $ratio = $widthOrig / $heightOrig ;

        $newwidth = $la;
        $newheight = $newwidth / $ratio;

        if ($newheight < $aut) {
            $newheight = $aut;
            $newwidth = $newheight * $ratio;
        }

        $x = $la - $newwidth;
        $y = $aut - $newheight;
        $x = $x < 0 ? $x / 2 : $x;//se x for menor que 2 eu divido ela por x
        $y = $y < 0 ? $y / 2 : $y;

        $finalImage = imagecreatetruecolor($la, $aut);
        switch ($file['type']) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = imagecreatefromjpeg($file['tmp_name']);
                break;
            case 'image/png':
                $image = imagecreatefrompng($file['tmp_name']);
                break;   
        }

        imagecopyresampled(
            $finalImage, $image,
            $x, $y,0,0,
            $newwidth, $newheight, $widthOrig ,$heightOrig
        );

        $fileName = md5(time().rand(0,9999).time().rand(0,9999)).'.jpg';


        imagejpeg($finalImage, $folder.'/'.$fileName);

        return $fileName;
    }
}
