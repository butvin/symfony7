<?php

namespace App\EventSubscriber;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\Token\JWTPostAuthenticationToken;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Ldap\Security\LdapUser;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\AuthenticationTokenCreatedEvent;

final readonly class OnTokenCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private JWTTokenManagerInterface $jwtTokenManager
    ) {}

    public function onAuthenticationTokenCreatedEvent(AuthenticationTokenCreatedEvent $event): void
    {
        $token = $event->getAuthenticatedToken();
        $user = $token->getUser();

        if ($user instanceof LdapUser && $token instanceof UsernamePasswordToken) {
            $fetchedRoles = [];
            $currentRoles = $user->getRoles();
            $memberOf = $user->getEntry()->getAttribute('memberOf');

            if (!empty($memberOf)) {
                foreach ($memberOf as $memberOfEntry) {
                    if (preg_match('/^cn=([^,]+)/', $memberOfEntry, $matches)) {
                        $fetchedRoles[] = 'ROLE_LDAP_' . mb_strtoupper(str_replace('-', '_', $matches[1]));
                    }
                }
            }

            if (!empty($fetchedRoles)) {
                $totalRoles = array_unique(array_merge($fetchedRoles, $currentRoles));

                $newUser = new LdapUser($user->getEntry(), $user->getUserIdentifier(), $user->getPassword(), $totalRoles);
                $newToken = new UsernamePasswordToken($newUser, $token->getFirewallName(), $totalRoles);


                $jwtTokenString = $this->jwtTokenManager->create($newUser);
                $tokenJWT = new JWTPostAuthenticationToken($newUser, $token->getFirewallName(), $totalRoles,  $jwtTokenString);
//                dump($t);
//                dd('1',$tokenJWT);

                $event->setAuthenticatedToken($tokenJWT);
            }
        }

        if ($user instanceof LdapUser && $token instanceof JWTPostAuthenticationToken) {
//            dd('2',$token);
            $jwtDecoded = $this->jwtTokenManager->parse($token->getCredentials());
            $totalRoles = $jwtDecoded['roles'];
            $newUser = new LdapUser($user->getEntry(), $user->getUserIdentifier(), $user->getPassword(), $totalRoles);
            $newToken = new JWTPostAuthenticationToken($newUser, $token->getFirewallName(), $totalRoles,  $token->getCredentials());
            $event->setAuthenticatedToken($newToken);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AuthenticationTokenCreatedEvent::class => 'onAuthenticationTokenCreatedEvent',
        ];
    }
}
