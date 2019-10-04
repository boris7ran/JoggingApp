<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterController extends AbstractController
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(
        ValidatorInterface $validator
    ) {
        $this->validator = $validator;
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
     * @return Response
     */
    public function store(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = new User();
        $user->setUsername($request->get('name'));
        $user->setPassword($request->get('password'));

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new Response($errorsString);
        }

        $entityManager->persist($user);

        $entityManager->flush();

        return new Response('Saved new user with id '.$user->getId());
    }
}
