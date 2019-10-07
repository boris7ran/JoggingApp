<?php

namespace App\Controller;

use App\Entity\Record;
use App\Entity\User;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
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

    public function myRecords()
    {
        $user = $this->getUser();

        return $this->render('records/show.html.twig', ['user' => $user]);
    }

    public function store(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $record = new Record();
        $date = \DateTime::createFromFormat("Y-m-d", $request->get('date'));
        $record->setDate($date);
        $record->setDistance($request->get('distance'));
        $record->setTime($request->get('time'));

        $user = $entityManager->getRepository(User::class)->find($id);
        $record->setUser($user);

        $entityManager->persist($record);

        $entityManager->flush();

        return new Response('Saved new record with id ' . $record->getId());
    }

    public function edit($id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $record = $entityManager->getRepository(Record::class)->find($id);

        return $this->render('records/edit.html.twig', ['record' => $record]);
    }

    public function put(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $record = $entityManager->getRepository(Record::class)->find($id);

        $date = \DateTime::createFromFormat("Y-m-d", $request->get('date'));
        $record->setDate($date);
        $record->setDistance($request->get('distance'));
        $record->setTime($request->get('time'));

        $entityManager->persist($record);

        $entityManager->flush();

        return new Response('Edited record with id ' . $record->getId());
    }

    public function delete($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $record = $entityManager->getRepository(Record::class)->find($id);

        $entityManager->remove($record);
        $entityManager->flush();

        return new Response('Deleted record with id: ' . $record->getId());
    }
}