<?php
namespace src\handlers;

use \src\models\Post;
use src\models\PostComment;
use src\models\PostLike;
use \src\models\User;
use \src\models\UserRelation;

class PostHandler {
/*===============================================================================*/
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
/*===============================================================================*/
/*===============================================================================*/
/*===============================================================================*/
    public static function _postListToObject($postsList,$loggedUserId){
        // 3 . transforma o resultado em obj dos models.
    $posts = [];
        foreach ($postsList as $postsItem) {
            $newPost = new Post();
            $newPost->id =$postsItem['id'];
            $newPost->type =$postsItem['type'];
            $newPost->created_post =$postsItem['created_post'];
            $newPost->body =$postsItem['body'];
            $newPost->mine = false;

            // 3.1 . verificar se a postagem e do usuario.
                if ($postsItem['id_user'] === $loggedUserId) {
                    $newPost->mine = true;
                }

      // 4 . preencher as info adcionais no post.
      $newUser = User::select()
          ->where('id', $postsItem['id_user'])
      ->one();
      $newPost->user = new User();
      $newPost->user->id = $newUser['id'];
      $newPost->user->name = $newUser['name'];
      $newPost->user->avatar = $newUser['avatar'];

      // 4.1 . preencher as info de LIKE.
            $likes = PostLike::select()
                ->where('id_post',$postsItem['id'])
            ->get();

        $newPost->likeCount = count($likes);//contar os likes
        $newPost->liked = self::isLiked($postsItem['id'], $loggedUserId);//pegar de quem e os likes


      // 4.2 . preencher as info de COMMENTS.
          $newPost->comments = PostComment::select()->where('id_post',$postsItem['id'])->get();//pegar os comentarios
          foreach ($newPost->comments as $key => $comment) {
            $newPost->comments[$key]['user'] = User::select()->where('id',$comment['id_user'])->one();
          }

            $posts[] = $newPost;
        }
        return $posts;
    }
/*===============================================================================*/
/*===============================================================================*/
    public static function isLiked($id, $loggedUserId){
        $mylike = PostLike::select()
                ->where('id_post', $id)
                ->where('id_user', $loggedUserId)
            ->get();
        if (count($mylike) > 0) {
            return true;
        }else{
            return false;
        }
        
    }
/*===============================================================================*/
/*===============================================================================*/
    public static function deleteLike($id, $loggedUserId){
       PostLike::delete()
                ->where('id_post', $id)
                ->where('id_user', $loggedUserId)
            ->execute();  
    }

/*===============================================================================*/
/*===============================================================================*/
    public static function addLike($id, $loggedUserId){
       PostLike::insert([
            'id_post'=> $id,
            'id_user'=> $loggedUserId,
            'created_likes'=> date('Y-m-d H:i:s')
        ])->execute();
    }

/*===============================================================================*/
/*===============================================================================*/
    public static function addComment($id, $txt, $idUser){
       PostComment::insert([
            'id_post'=> $id,
            'id_user'=> $idUser,
            'created_comment'=> date('Y-m-d H:i:s'),
            'body'=> $txt
        ])->execute();
    }

/*===============================================================================*/
/*===============================================================================*/
    public static function getUserFedd($idUser, $page, $loggedUserId){
    
    $perPage = 4;

         // 2 . pegar post ordenado pela data
       $postsList = Post::select()
            ->where('id_user', $idUser)
            ->orderBy('created_post', 'desc')
            ->page($page, $perPage)
        ->get();

  $total = Post::select()
        ->where('id_user', $idUser)
    ->count();
   $pageCount = ceil($total / $perPage);
   
  // 3 . transforma o resultado em obj dos models.
  $posts = self::_postListToObject($postsList,$loggedUserId);
   
  
  // 5 . retornar o resultado
   return ['posts'=> $posts, 'pageCount'=> $pageCount, 'currentPage'=> $page];
        
    }
/*===============================================================================*/
/*===============================================================================*/
    public static function getHomeFeed($idUser,$page){
        $perPage = 4;

       // 1 . pegar lista de usuarios que eu sigo.
            /*pegar tudo que user_from sou eu
            ou seja todos os 
            user_to vao ser todas 
            as pessoas que eu segui */
        $usersList = UserRelation::select()
            ->where('user_from', $idUser)
        ->get();

        $users = [];
        foreach ($usersList as $userItem) {
            $users []= $userItem['user_to'];
        }
        $users[] = $idUser;
        //adicionar eu pagar mostrar o meu post

       // 2 . pegar post ordenado pela data
       $postsList = Post::select()
            ->where('id_user', 'in', $users)
            ->orderBy('created_post', 'desc')
            ->page($page, $perPage)
        ->get();

       $total = Post::select()
                ->where('id_user', 'in', $users)
            ->count();
        $pageCount = ceil($total / $perPage);
        
       // 3 . transforma o resultado em obj dos models.
       $posts = self::_postListToObject($postsList,$idUser);
       //puxar da funcao ajudadadora
        
       
       // 5 . retornar o resultado
        return ['posts'=> $posts, 'pageCount'=> $pageCount, 'currentPage'=> $page];
    }
/*===============================================================================*/
/*===============================================================================*/
    public static function getPhotosFrom($id){
        $photosData = Post::select()
            ->where('id_user', $id)
            ->where('type', 'photo')
        ->get();

        $photos = [];

        foreach ($photosData as $photo) {
            $newPost = new Post();
            $newPost->id = $photo['id'];
            $newPost->type = $photo['type'];
            $newPost->created_post = $photo['created_post'];
            $newPost->body = $photo['body'];

            $photos[] = $newPost;
        }

        return $photos;
    }

/*===============================================================================*/
/*===============================================================================*/
    public static function delete($idPost, $idUser){
        //1. verificar se o poste existe e se e do usuario logado
        $post = Post::select()
            ->where('id',$idPost)
            ->where('id_user',$idUser)
        ->get();
        
        if (count($post) > 0) {
            $post = $post[0];

            //2. deletar os likes e comments
            PostLike::delete()->where('id_post', $idPost)->execute();
            PostComment::delete()->where('id_post', $idPost)->execute();

            //3. se a foto for type photo deletar o arquivo
            if ($post['type'] === 'photo') {
                $img = __DIR__.'/../../public/media/uploads/'.$post['body'];
                if (file_exists($img)) {
                    unlink($img);
                }
            }

            //4. deletar o post
            Post::delete()->where('id', $idPost)->execute();
        }   
    }
/*===============================================================================*/
/*===============================================================================*/
}