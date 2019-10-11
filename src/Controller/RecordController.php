<?php

namespace App\Controller;

use App\Services\RecordsService;
use App\Services\UsersService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RecordController extends AbstractController
{
    /**
     * @var RecordsService
     */
    private $recordsService;
    /**
     * @var UsersService
     */
    private $usersService;
    /**
     * @var User
     */
    private $loggedUser;

    /**
     * RecordController constructor.
     *
     * @param RecordsService $recordsService
     * @param UsersService $usersService
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(RecordsService $recordsService, UsersService $usersService, TokenStorageInterface $tokenStorage)
    {
        $this->recordsService = $recordsService;
        $this->loggedUser = $tokenStorage->getToken()->getUser();
        $this->usersService = $usersService;
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
        $records = $this->recordsService->getUserRecords($id, $startDate, $endDate)->getRecords();
        $user = $this->usersService->getUser($id);
        $reports = $records ? $this->recordsService->makeReports($user->getId()) : [];
        $this->denyAccessUnlessGranted('view', $user);

        return $this->render('records/show.html.twig', ['user' => $user, 'records' => $records, 'reports' => $reports]);
    }

    /**
     * @return Response
     *
     * @throws Exception
     */
    public function myRecords(): Response
    {
        $records = $this->recordsService->getUserRecords($this->loggedUser->getId())->getRecords();
        $user = $this->usersService->getUser($this->loggedUser->getId());
        $reports = $records ? $this->recordsService->makeReports($this->loggedUser->getId()) : [];

        return $this->render('records/show.html.twig', ['user' => $user, 'records' => $records, 'reports' => $reports]);
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
     *
     * @return RedirectResponse
     */
    public function delete(int $id): RedirectResponse
    {
        $record = $this->recordsService->deleteRecord($id);

        return $this->redirectToRoute('show_records', ['id' => $record->getUser()->getId()]);
    }
}