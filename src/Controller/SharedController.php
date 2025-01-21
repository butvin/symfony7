<?php

namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Ldap\Security\LdapUser;
use Symfony\Component\Routing\Attribute\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SharedController extends AbstractController
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorageInterface,
        private readonly JWTTokenManagerInterface $jwtManager
    ) {}

    /**
     * @throws JWTDecodeFailureException
     */
    #[Route('/shared/index', name: 'app_shared_index', methods: ['GET'])]
    public function index(): Response
    {
        $token = $this->tokenStorageInterface->getToken();
        $decodedJwtToken = $this->jwtManager->decode($token);

        //$user = $token->getUser();
        $roles = $token->getUser()->getRoles();

        return $this->render('shared/index.html.twig', [
            'controller_name' => 'SharedController',
            'action' => 'index',
            'roles' => $roles,
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
