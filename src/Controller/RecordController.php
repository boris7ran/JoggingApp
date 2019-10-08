<?php

namespace App\Controller;

use App\Services\RecordsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class RecordController extends AbstractController
{
    private $recordsService;

    public function __construct(RecordsService $recordsService)
    {
        $this->recordsService = $recordsService;
    }

    public function show($id)
    {
        $user = $this->recordsService->getUserRecords($id);

        return $this->render('records/show.html.twig', ['user' => $user]);
    }

    public function myRecords()
    {
        $user = $this->recordsService->getUserRecords();

        return $this->render('records/show.html.twig', ['user' => $user]);
    }

    public function store(Request $request, $id)
    {
        $this->recordsService->storeNewRecord($request, $id);

        return $this->redirectToRoute('show_records', ['id' => $id]);
    }

    public function edit($id)
    {
        $record = $this->recordsService->editRecord($id);

        return $this->render('records/edit.html.twig', ['record' => $record]);
    }

    public function put(Request $request, $id)
    {
        $record = $this->recordsService->putEditedRecord($request, $id);

        return $this->redirectToRoute('show_records', ['id' => $record->getUser()->getId()]);
    }

    public function delete($id)
    {
        $record = $this->recordsService->deleteRecord($id);

        return $this->redirectToRoute('show_records', ['id' => $record->getUser()->getId()]);
    }
}