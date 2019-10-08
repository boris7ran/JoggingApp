<?php

namespace App\Controller;

use App\Services\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    /**
     * @var AuthService
     */
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @return Response
     */
    public function index()
    {
        return $this->render('auth\register.html.twig');
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return RedirectResponse
     */
    public function store(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->authService->storeUser($request, $passwordEncoder);

        return $this->redirectToRoute('login_user');
    }
}
