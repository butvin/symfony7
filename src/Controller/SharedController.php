<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Ldap\Security\LdapUser;
use Symfony\Component\Routing\Attribute\Route;

class SharedController extends AbstractController
{
    #[Route('/shared/index', name: 'app_shared_index', methods: ['GET'])]
    public function index(): Response
    {
        $addedRoles = [];
        $user = $this->getUser();
        if ($user instanceof LdapUser) {
            $memberOf = $user->getEntry()->getAttribute('memberOf');
            if(is_array($memberOf) && !empty($memberOf)) {
                foreach ($memberOf as $memberOfItem) {
                    if (preg_match('/^cn=([^,]+)/', $memberOfItem, $matches)) {
                        $addedRoles[] = 'ROLE_LDAP_' . mb_strtoupper(str_replace('-', '_', $matches[1]));
                    }
                }
            }
        }

        return $this->render('shared/index.html.twig', [
            'controller_name' => 'SharedController',
            'action' => 'index',
            'roles' => $addedRoles,
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
