<?php
namespace src\handlers;

use \src\models\User;

class LoginHandler {

    public static function checkLogin(){
        if (!empty($_SESSION['token'])) {
            $token = $_SESSION['token'];

            $data = User::select()->where('token', $token)->one();
            if (count($data) > 0) {
                $logado = new User();
                $logado->id = $data['id'];
                $logado->email = $data['email'];
                $logado->name = $data['name'];

                return $logado;
            }
        }
        return false;
    }
    
    public static function verifyLogin($email, $pass){
        $user = User::select()
        ->where('email', $email)
        ->one();

        if ($user) {
            if (password_verify($pass, $user['password'])) {
                $token = md5(time().rand(0,9999).time());

                User::update()
                ->set('token', $token)
                ->where('email', $email)
                ->execute();

                return $token;
            }
        }
        return false;
    }

    public static function emailExists($email){
        $user = User::select()
        ->where('email', $email)
        ->one();

        if (cond) {
        
        }

        return false;
    }

    public static function addUser($name, $email, $pass, $birthdate)
    {
        # code...
    }
}