<?php $render('header',['loggedUser' => $loggedUser]); ?>
    <section class="container main">
        <?php $render('sidebar', ['activeMenu'=>'photos']); ?>

        <section class="feed">

        <?php $render('perfil-header', ['user'=>$user, 'loggedUser'=>$loggedUser, 'isFlollowing'=>$isFlollowing]); ?>


        <div class="row">
            <div class="column">
                        
                <div class="box">
                    <div class="box-body">
                        <div class="full-user-photos">

                            <?php if(count($user->photos) === 0): ?>
                                Este usuario não possui fotos.
                            <?php endif; ?>
                            
                            <?php foreach ($user->photos as $photos):?>

                                <div class="user-photo-item">
                                    <a href="#modal-<?=$photos->id;?>" rel="modal:open">
                                        <img src="<?=$base;?>/media/uploads/<?=$photos->body;?>" />
                                    </a>
                                    <div id="modal-<?=$photos->id;?>" style="display:none">
                                        <img src="<?=$base;?>/media/uploads/<?=$photos->body;?>" />
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        </div>
                    </div>
                </div>

            </div>
        
        </div>

        </section>
    
        </section>
    
<?php $render('footer'); ?>