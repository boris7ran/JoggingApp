<?php

namespace App\Controller;

use App\Services\RecordsService;
use App\Services\UsersService;
use Doctrine\ORM\ORMException;
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
     * @param int $userId
     * @param Request $request
     *
     * @return Response
     *
     * @throws Exception
     */
    public function show(Request $request, int $userId): Response
    {
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');
        $records = $this->recordsService
            ->getUserRecords($userId, $startDate, $endDate)
            ->getRecords();
        $user = $this->usersService->getUser($userId);
        $reports = $records ? $this->recordsService->makeReports($user->getId()) : [];
        $this->denyAccessUnlessGranted('view', $user);

        return $this->render('records/show.html.twig',
            ['user' => $user, 'records' => $records, 'reports' => $reports]
        );
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

        return $this->render(
            'records/show.html.twig',
            ['user' => $user, 'records' => $records, 'reports' => $reports]
        );
    }

    /**
     * @param Request $request
     * @param int $userId
     *
     * @return RedirectResponse
     */
    public function store(Request $request, int $userId): RedirectResponse
    {
        $date = \DateTime::createFromFormat('Y-m-d', $request->get('date'));
        $time = $request->get('time');
        $distance = $request->get('distance');
        $this->recordsService->storeNewRecord($date, $time, $distance, $userId);

        return $this->redirectToRoute('show_records', ['userId' => $userId]);
    }

    /**
     * @param int $recordId
     *
     * @return Response
     */
    public function edit(int $recordId): Response
    {
        $record = $this->recordsService->getRecord($recordId);

        return $this->render('records/edit.html.twig', ['record' => $record]);
    }

    /**
     * @param Request $request
     * @param int $recordId
     *
     * @return RedirectResponse
     */
    public function put(Request $request, int $recordId): RedirectResponse
    {
        $date = \DateTime::createFromFormat('Y-m-d', $request->get('date'));
        $time = $request->get('time');
        $distance = $request->get('distance');
        $record = $this->recordsService->editRecord($date, $time, $distance, $recordId);

        return $this->redirectToRoute('show_records', ['userId' => $record->getUser()->getId()]);
    }

    /**
     * @param int $recordId
     *
     * @return RedirectResponse
     */
    public function delete(int $recordId): RedirectResponse
    {
        $record = $this->recordsService->deleteRecord($recordId);

        return $this->redirectToRoute('show_records', ['userId' => $record->getUser()->getId()]);
    }
}
