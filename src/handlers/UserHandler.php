<?php
namespace src\handlers;

use \src\models\User;
use \src\models\UserRelation;

class UserHandler {

    public static function checkLogin(){
        if (!empty($_SESSION['token'])) {
            $token = $_SESSION['token'];

            $data = User::select()->where('token', $token)->one();
            if ($data > 0) {
                $logado = new User();
                $logado->id = $data['id'];
                $logado->email = $data['email'];
                $logado->name = $data['name'];
                $logado->birthdate = $data['birthdate'];
                $logado->avatar = $data['avatar'];

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

        return $user ? true : false;
        /*se ele achou o email ele retorna true se nao retorna false*/
    }
    
    public static function idExists($id){
        $user = User::select()
        ->where('id', $id)
        ->one();

        return $user ? true : false;
        /*se ele achou o email ele retorna true se nao retorna false*/
    }

     public function getUser($id, $full = false){
       $data = User::select()->where('id',$id)->one();
       if ($data) {
            $user = new User();
            $user->id = $data['id'];
            $user->name = $data['name'];
            $user->avatar = $data['avatar'];
            $user->cover =  $data['cover'];
            $user->birthdate =  $data['birthdate'];
            $user->city =  $data['city'];
            $user->work =  $data['work'];

            if ($full) {
                $user->followers = []; //seguidores
                $user->following = []; //seguindo
                $user->photos = [];

                //followers
                $followers = UserRelation::select()
                    ->where('user_to', $id)
                ->get();
                foreach ($followers as $follower) {

                    $userData = User::select()
                        ->where('id', $follower['user_from'])
                    ->one();

                    $newUser = new User();
                    $newUser->id = $userData['id'];
                    $newUser->name = $userData['name'];
                    $newUser->avatar = $userData['avatar'];

                    $user->followers[] = $newUser;
                }
                //following
                $followings = UserRelation::select()
                    ->where('user_from', $id)
                ->get();
                foreach ($followings as $following) {

                    $userData = User::select()
                        ->where('id', $following['user_to'])
                    ->one();

                    $newUser = new User();
                    $newUser->id = $userData['id'];
                    $newUser->name = $userData['name'];
                    $newUser->avatar = $userData['avatar'];

                    $user->following[] = $newUser;
                }
                //photos

            }

            return $user;
       }

       return false;
    }

    public  function addUser($name, $email, $pass, $birthdate){
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $token = md5(time().rand(0,9999).time());

        User::insert([
          'email'=> $email,  
          'password'=> $hash,  
          'name'=> $name,  
          'birthdate'=> $birthdate,  
          'avatar'=> 'default.jpg',  
          'cover'=> 'cover.jpg',
          'token'=> $token
        ])->execute();

        return $token;
    }
}