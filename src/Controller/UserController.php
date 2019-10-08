<?php

namespace App\Controller;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
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

    public function index()
    {
        $entityManager = $this->getDoctrine()->getManager();

        if ($this->getUser()->getRole() === 'ROLE_ADMIN') {
            $users = $entityManager->getRepository(User::class)->findAll();
        } else {
            $users = $entityManager->getRepository(User::class)->findBy(['role' => 'ROLE_USER']);
        }

        return $this->render('users/index.html.twig', ['users' => $users]);
    }

    public function upgrade(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = $entityManager->getRepository(User::class)->find($id);

        $user->setRole($request->get('role'));

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new Response($errorsString);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('show_records', ['id' => $user->getId()]);
    }
}