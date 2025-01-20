<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SharedController extends AbstractController
{
    #[Route('/shared/index', name: 'app_shared_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('shared/index.html.twig', [
            'controller_name' => 'SharedController',
            'action' => 'index',
        ]);
    }

    #[Route('/shared/list', name: 'app_shared_list', methods: ['GET'])]
    public function list(): Response
    {
        return $this->render('shared/list.html.twig', [
            'controller_name' => 'SharedController',
            'action' => 'list',
        ]);
    }
}
