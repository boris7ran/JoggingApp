<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\Interfaces\UserRepositoryInterface;
use Error;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthService
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepo;

    /**
     * AuthService constructor.
     *
     * @param ValidatorInterface $validator
     * @param UserRepositoryInterface $userRepo
     */
    public function __construct(ValidatorInterface $validator, UserRepositoryInterface $userRepo)
    {
        $this->validator = $validator;
        $this->userRepo = $userRepo;
    }

    /**
     * @param string $username
     * @param string $password
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return User|Response
     */
    public function storeUser(string $username, string $password, UserPasswordEncoderInterface $passwordEncoder)
    {
        $roles[] = "ROLE_USER";

        $user = new User($password, $username, $roles);

        $password = $passwordEncoder->encodePassword($user, $password);
        $user->setPassword($password);

        $errors = $this->validator->validate($user, null, ['registration']);

        if (count($errors) > 0) {
            throw new Error('This user is not valid!');
        }

        $this->userRepo->add($user);

        return $user;
    }
}