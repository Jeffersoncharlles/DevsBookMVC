<?php
namespace src\handlers;

use \src\models\Post;

class PostHandler {

    public static function addPost($user, $type, $body){
        $date = date('Y-m-d H:i:s');
        $body = trim($body);

        if (!empty($user) && !empty($body)) {
            Post::insert([
                'id_user'=> $user,
                'type'   => $type,
                'created_post' => $date,
                'body'   => $body
            ])->execute();
        }
    }

    public function FunctionName(){
        # code...
    }
}