<?php

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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
    public function storeUser(Request $request, UserPasswordEncoderInterface $passwordEncoder): User
    {
        $user = new User();
        $user->setUsername($request->get('name'));
        $user->setPassword($request->get('password'));

        if ($this->em->getRepository(User::class)->findBy(['role' => 'ROLE_ADMIN'])) {
            $user->setRole("ROLE_USER");
        } else {
            $user->setRole("ROLE_ADMIN");
        }

        $password = $passwordEncoder->encodePassword($user, $user->getPassword());
        $user->setPassword($password);

        $errors = $this->validator->validate($user, null, ['registration']);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new Response($errorsString);
        }

        $this->em->persist($user);

        $this->em->flush();

        return $user;
    }
}