<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    #[Route('/auth/login', name: 'app_auth_login')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
//        if ($this->getUser()) {
//            return $this->redirectToRoute('app_shared_list');
//        }

        return $this->render('auth/login.html.twig', [
            'controller_name' => 'Ldap Authentication',
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/auth/logout', name: 'app_auth_logout')]
    public function logout(): void
    {
        throw new \LogicException('This logout method should be intercepted by the firewall.');
    }

    #[Route('/auth/check', name: 'app_auth_check', methods: ['GET'])]
    public function check(): void
    {
        throw new \LogicException('This check method should be intercepted by the firewall.');
    }

}
