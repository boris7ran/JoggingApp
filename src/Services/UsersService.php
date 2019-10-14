<?php

namespace App\Services;

use App\DataTransferObjects\ListUsersDto;
use App\DataTransferObjects\UserDto;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UsersService
{
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
     * @var UserRepository
     */
    private $userRepo;

    /**
     * UsersService constructor.
     * @param UserRepository $userRepo
     * @param TokenStorageInterface $tokenStorage
     * @param ValidatorInterface $validator
     */
    public function __construct(UserRepository $userRepo, TokenStorageInterface $tokenStorage, ValidatorInterface $validator)
    {
        $this->userRepo = $userRepo;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->validator = $validator;
    }

    public function getUser(int $userId)
    {
        $user = $this->userRepo->ofId($userId);

        return new UserDto($user);
    }

    /**
     * @return ListUsersDto
     */
    public function getUsers(): ListUsersDto
    {
        if (in_array('ROLE_ADMIN', $this->user->getRoles())) {
            $users = $this->userRepo->findAll();
        } else {
            $users = $this->userRepo->findBy(['roles' => ['ROLE_USER']]);
        }

        return new ListUsersDto($users);
    }

    /**
     * @param array $role
     * @param int $id
     *
     * @return object
     *
     * @throws NonUniqueResultException
     * @throws ORMException
     */
    public function upgradeUser(array $role, int $id): object
    {
        $user = $this->userRepo->ofId($id);

        $user->setRoles($role);

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new Response($errorsString);
        }

        $this->userRepo->add($user);

        return new UserDto($user);
    }
}
