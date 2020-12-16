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
}