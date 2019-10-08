<?php

namespace App\Controller;

use App\Services\RecordsService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordController extends AbstractController
{
    /**
     * @var RecordsService
     */
    private $recordsService;

    /**
     * RecordController constructor.
     * @param RecordsService $recordsService
     */
    public function __construct(RecordsService $recordsService)
    {
        $this->recordsService = $recordsService;
    }

    /**
     * @param int $id
     * @param Request $request
     *
     * @return Response
     *
     * @throws Exception
     */
    public function show(int $id, Request $request): Response
    {
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');
        $user = $this->recordsService->getUserRecords($id, $startDate, $endDate);

        return $this->render('records/show.html.twig', ['user' => $user]);
    }

    /**
     * @return Response
     *
     * @throws Exception
     */
    public function myRecords(): Response
    {
        $user = $this->recordsService->getUserRecords();

        return $this->render('records/show.html.twig', ['user' => $user]);
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function store(Request $request, int $id): RedirectResponse
    {
        $this->recordsService->storeNewRecord($request, $id);

        return $this->redirectToRoute('show_records', ['id' => $id]);
    }

    /**
     * @param int $id
     *
     * @return Response
     */
    public function edit(int $id): Response
    {
        $record = $this->recordsService->editRecord($id);

        return $this->render('records/edit.html.twig', ['record' => $record]);
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function put(Request $request, int $id): RedirectResponse
    {
        $record = $this->recordsService->putEditedRecord($request, $id);

        return $this->redirectToRoute('show_records', ['id' => $record->getUser()->getId()]);
    }

    /**
     * @param int $id
     * @return RedirectResponse
     */
    public function delete(int $id): RedirectResponse
    {
        $record = $this->recordsService->deleteRecord($id);

        return $this->redirectToRoute('show_records', ['id' => $record->getUser()->getId()]);
    }
}