<?php

namespace App\Controller;

use App\Entity\User;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class RecordController extends AbstractController
{
    public function show($id)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        if (!$user) {
            throw new Exception('No record found with id ' . $id);
        }

        return $this->render('records/show.html.twig', ['user' => $user]);
    }
}