<?php

namespace App\Controller;

use App\Services\UsersService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    public function index()
    {
        $users = $this->usersService->getUsers();

        return $this->render('users/index.html.twig', ['users' => $users]);
    }

    public function upgrade(Request $request, $id)
    {
        $user = $this->usersService->upgradeUser($request, $id);

        return $this->redirectToRoute('show_records', ['id' => $user->getId()]);
    }
}