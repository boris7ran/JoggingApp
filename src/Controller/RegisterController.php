<?php

namespace App\Controller;

use App\Services\UsersService;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends AbstractController
{
    /**
     * @var UsersService
     */
    private $usersService;

    /**
     * RegisterController constructor.
     *
     * @param UsersService $usersService
     */
    public function __construct(UsersService $usersService)
    {
        $this->usersService = $usersService;
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
     *
     * @return RedirectResponse
     *
     * @throws ORMException
     */
    public function store(Request $request)
    {
        $username = $request->get('name');
        $password = $request->get('password');
        $this->usersService->storeUser($username, $password);

        return $this->redirectToRoute('login_user');
    }
}
