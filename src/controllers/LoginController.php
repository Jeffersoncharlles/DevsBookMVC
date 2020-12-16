<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\LoginHandler;

class LoginController extends Controller {


    public function signin() {
        $flash = '';
        if (!empty($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }

        $this->render('login', [
            'flash'=> $flash
        ]);
    }

    public function signinAction() {

        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $pass = filter_input(INPUT_POST, 'password');

            if ($email && $pass) {
                $token = LoginHandler::verifyLogin($email, $pass);
                if ($token) {
                    $_SESSION['token'] = $token;
                    $this->redirect('/');
                }else{
                    $_SESSION['flash'] = 'E-mail e/ou senha nao conferem!';
                    $this->redirect('/login');
                }
            }else{
                $this->redirect('/login');
            }    
    }

    public function signup() {

        $flash = '';
        if (!empty($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }
        $this->render('cadastro', [
            'flash'=> $flash
        ]);
    }

    public function signupAction(){
        
        $name = filter_input(INPUT_POST, 'name');
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $pass = filter_input(INPUT_POST, 'password');
        $birthdate = filter_input(INPUT_POST, 'birthdate');

        if ($name && $email && $pass && $birthdate) {
            $birthdate = explode('/',$birthdate);
            if (count($birthdate) !=3) {
                $_SESSION['flash'] = 'Data de Nascimento inválida!';
                $this->redirect('/cadastro');
            }
            $birthdate = $birthdate[2].'-'.$birthdate[1].'-'.$birthdate[0];
            if (strtotime($birthdate) === false) {
                $_SESSION['flash'] = 'Data de Nascimento inválida!';
                $this->redirect('/cadastro');
            }

            if (LoginHandler::emailExists($email) === false) {
                $token = LoginHandler::addUser($name, $email, $pass, $birthdate);
                $_SESSION['token'] = $token;
            }
        }
    }

}