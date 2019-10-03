<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends AbstractController
{
    /**
     * @return Response
     */
    public function index()
    {
        return $this->render('register.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = new User();
        $user->setUsername($request->get('name'));
        $user->setPassword($request->get('password'));

        $entityManager->persist($user);

        $entityManager->flush();

        return new Response('Saved new user with id '.$user->getId());
    }
}
