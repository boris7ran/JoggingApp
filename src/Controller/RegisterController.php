<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class RegisterController extends AbstractController
{
    public function index()
    {
        return $this->render('register.html.twig');
    }

    public function store(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = new User();
        $user->setUsername($request->get('name'));
        $user->setPassword($request->get('password'));

        $entityManager->persist($user);

        $entityManager->flush();

        return new Response('Saved new product with id '.$user->getId());
    }
}
