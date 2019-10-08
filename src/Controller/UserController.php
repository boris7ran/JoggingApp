<?php

namespace App\Controller;

use App\Services\UsersService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    /**
     * @var UsersService
     */
    private $usersService;

    public function __construct(
        UsersService $usersService
    ) {
        $this->usersService = $usersService;
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        $users = $this->usersService->getUsers();

        return $this->render('users/index.html.twig', ['users' => $users]);
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function upgrade(Request $request, int $id): RedirectResponse
    {
        $user = $this->usersService->upgradeUser($request, $id);

        return $this->redirectToRoute('show_records', ['id' => $user->getId()]);
    }
}