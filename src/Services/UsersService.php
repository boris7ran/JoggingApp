<?php

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UsersService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var User
     */
    private $user;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * UsersService constructor.
     * @param EntityManagerInterface $em
     * @param TokenStorageInterface $tokenStorage
     * @param ValidatorInterface $validator
     */
    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->validator = $validator;
    }

    /**
     * @return User[]
     */
    public function getUsers(): array
    {
        if ($this->user->getRole() === 'ROLE_ADMIN') {
            $users = $this->em->getRepository(User::class)->findAll();
        } else {
            $users = $this->em->getRepository(User::class)->findBy(['role' => 'ROLE_USER']);
        }

        return $users;
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @return User
     */
    public function upgradeUser(Request $request, int $id): User
    {
        $user = $this->em->getRepository(User::class)->find($id);

        $user->setRole($request->get('role'));

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new Response($errorsString);
        }

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}