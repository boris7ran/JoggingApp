<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LoginController extends AbstractController
{
    public function index()
    {
        return $this->render('auth\login.html.twig');
    }
}
