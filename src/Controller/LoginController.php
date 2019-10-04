<?php

namespace App\Controller;

use App\Entity\User;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws Exception
     */
    public function store(Request $request)
    {
        $requestUsername = $request->get('name');

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['username' => $requestUsername]);

        if (!$user) {
            throw new Exception('No user found with username: ' . $requestUsername );
        } elseif ($user->getPassword() !== $request->get('password')) {
            throw new Exception('Incorrect password');
        }

        return new Response('User found with username: '.$user->getUsername());
    }
}
