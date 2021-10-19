<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'login';

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function authenticate(Request $request): PassportInterface
    {
        $email = $request->request->get('email', '');
        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $roles = $token->getUser()->getRoles();
        $status = $token->getUser()->getStatus();
        $rolesTab = array_map(function ($role) {
            return $role;
        }, $roles);
//        $slug = $token->getUser()->getDepartemnt()->getCodeDeppart() ;

        if (in_array(USER::ROLE_SUPER_ADMIN, $rolesTab, true) && $status)
            return new RedirectResponse($this->urlGenerator->generate('dashboard_super_admin'));
      elseif  (in_array(USER::ROLE_ADMIN, $rolesTab, true) && $status)
            return new RedirectResponse($this->urlGenerator->generate('dashboard_admin'));
        elseif (in_array(USER::ROLE_RESPONSABLE, $rolesTab, true) && $status)
            return new RedirectResponse($this->urlGenerator->generate('dashboard_responsable'));
        elseif (in_array(USER::ROLE_GERANT, $rolesTab, true) && $status)
            return new RedirectResponse($this->urlGenerator->generate('dashboard_gerant'));
        else
            return new RedirectResponse($this->urlGenerator->generate('login'));


//        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
//            return new RedirectResponse($targetPath);
//        }
//
//        // For example:
//        return new RedirectResponse($this->urlGenerator->generate('login'));
        // throw new \Exception('TODO: provide a valid redirect inside '.__FILE__');
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
