<?php

namespace App\Controller;

use App\Services\UsersService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        $users = $this->usersService->getUsers()->getUsers();

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
        $newRole[] = $request->get('role');
        $user = $this->usersService->upgradeUser($newRole, $id);

        return $this->redirectToRoute('all_users');
    }
}
