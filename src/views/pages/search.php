<?php $render('header',['loggedUser' => $loggedUser]); ?>
    <section class="container main">
        <?php $render('sidebar', ['activeMenu'=>'search']); ?>

        <section class="feed">
            <h1>Você pesquisou por: <?=$searchTerm;?> </h1>
        </section>
    </section>
<?php $render('footer'); ?>