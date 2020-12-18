<?php
namespace src\handlers;

use \src\models\User;
use \src\models\UserRelation;
use \src\handlers\PostHandler;

class UserHandler {
/*===============================================================================*/
/*===============================================================================*/
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
/*===============================================================================*/
/*===============================================================================*/    
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
/*===============================================================================*/
/*===============================================================================*/
    public static function emailExists($email){
        $user = User::select()
        ->where('email', $email)
        ->one();

        return $user ? true : false;
        /*se ele achou o email ele retorna true se nao retorna false*/
    }
/*===============================================================================*/
/*===============================================================================*/   
    public static function idExists($id){
        $user = User::select()
        ->where('id', $id)
        ->one();

        return $user ? true : false;
        /*se ele achou o email ele retorna true se nao retorna false*/
    }
/*===============================================================================*/
/*===============================================================================*/
     public static function getUser($id, $full = false){
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
            $user->email =  $data['email'];

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
                $user->photos = PostHandler::getPhotosFrom($id);
                


            }

            return $user;
       }

       return false;
    }
/*===============================================================================*/
/*===============================================================================*/
    public static function addUser($name, $email, $pass, $birthdate){
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
/*===============================================================================*/
/*===============================================================================*/
    public static function isFollowing($from, $to){

       $data = UserRelation::select()
            ->where('user_from', $from)
            ->where('user_to', $to)
        ->one();

        if($data) {
            return true;
        }
        return false;
    }

/*===============================================================================*/
/*===============================================================================*/
    public static function follow($from, $to){
        # seguir
        UserRelation::insert([
            'user_from' => $from,
            'user_to' => $to
        ])->execute();
    }
/*===============================================================================*/
/*===============================================================================*/
    public static function unfollow($from, $to){
        #remover seguidores
        UserRelation::delete()
            ->where('user_from', $from)
            ->where('user_to',$to)
        ->execute();
    }
/*===============================================================================*/
/*===============================================================================*/
    public static function searchUser($sear){
        $users = [];

        $data = User::select()
            ->where('name', 'like', '%'.$sear.'%')
        ->get();

        if ($data) {
            foreach ($data as $user) {
                $newUser = new User();
                $newUser->id = $user['id'];
                $newUser->name = $user['name'];
                $newUser->avatar = $user['avatar'];

                $users[] = $newUser;
            }
        }
        return $users;
    }
/*===============================================================================*/
/*===============================================================================*/
    public static function updateName($id, $name) {
        $user = User::select()->where('id', $id)->one();
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        if($name){
            User::update()
                ->set('name', $name)
                ->where('id', $id)
            ->execute();
        }
    }
/*===============================================================================*/
/*===============================================================================*/
    public static function updateEmail($id, $email) {
        $user = User::select()->where('id', $id)->one();
        
        if($email){
            User::update()
                ->set('email', $email)
                ->where('id', $id)
            ->execute();
        }
    }
/*===============================================================================*/
/*===============================================================================*/
    public static function updateBirthdate($id, $birthdate) {
        $user = User::select()->where('id', $id)->one();
        
        if($birthdate){
            User::update()
                ->set('birthdate', $birthdate)
                ->where('id', $id)
            ->execute();
        }
    }
/*===============================================================================*/
/*===============================================================================*/
    public static function updateCity($id, $city) {
        $user = User::select()->where('id', $id)->one();
        
        if($city){
            User::update()
                ->set('city', $city)
                ->where('id', $id)
            ->execute();
        }
    }
/*===============================================================================*/
/*===============================================================================*/
    public static function updateWork($id, $work) {
        $user = User::select()->where('id', $id)->one();
        
        if($work){
            User::update()
                ->set('work', $work)
                ->where('id', $id)
            ->execute();
        }
    }
/*===============================================================================*/
/*===============================================================================*/
    public static function updatePassword($id, $password) {
        $user = User::select()->where('id', $id)->one();
        
        
        if($hash){
            User::update()
                ->set('password', $hash)
                ->where('id', $id)
            ->execute();
        }
    }
/*===============================================================================*/
/*===============================================================================*/
}