<?php
namespace src\controllers;

use \core\Controller;

class LoginController extends Controller {


    public function signin() {


        $this->render('login', ['nome' => 'Bonieky']);
    }
    public function signup() {


        $this->render('cadastro', ['nome' => 'Bonieky']);
    }

}