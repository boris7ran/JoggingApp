<?php

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * AuthService constructor.
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $em
     */
    public function __construct(ValidatorInterface $validator, EntityManagerInterface $em)
    {
        $this->validator = $validator;
        $this->em = $em;
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return User|Response
     */
    public function storeUser(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $password = $request->get('password');
        $username = $request->get('name');


        $roles[] = "ROLE_USER";

        $user = new User($password, $username, $roles);

        $password = $passwordEncoder->encodePassword($user, $password);

        $user->setPassword($password);

        $errors = $this->validator->validate($user, null, ['registration']);

        if (count($errors) > 0) {
            throw new Error('This user is not valid!');
        }

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}