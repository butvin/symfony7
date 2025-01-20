<?php

namespace App\Controller\Public;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PublicController extends AbstractController
{
    public function login(): Response
    {
        return $this->render('public/login.html.twig', []);
    }

    public function logout():Response
    {
        return $this->redirectToRoute('app_shared_index');
    }
}
