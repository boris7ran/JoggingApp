<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends AbstractController
{
    /**
     * @return Response
     */
    public function index()
    {
        return $this->render('auth\login.html.twig');
    }
}
