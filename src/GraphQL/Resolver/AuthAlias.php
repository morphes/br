<?php
namespace App\GraphQL\Resolver;

use Doctrine\ORM\EntityManager;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Service\AuthenticatorService;

class AuthAlias implements ResolverInterface, AliasedInterface {

    public $user;

    public $em;

    private $request;

    private $authenticatorService;

    public function __construct(
        EntityManager $em,
        ContainerInterface $container,
        AuthenticatorService $authenticatorService
    ) {
        $this->em = $em;
        $this->authenticatorService = $authenticatorService;

        if ($container->has('request_stack')) {
            $this->request = $container->get('request_stack')->getCurrentRequest();

            $token = $authenticatorService->setRequest($this->request)->getAuth('token');

            if($token && $user = $authenticatorService->authByToken($token)) {
                $this->user = $user;
                return;
            }
        }
    }

    public function getAuth($type)
    {
        return $this->authenticatorService->getAuth($type);
    }

    public function getUser()
    {
        if($this->user) {
            return $this->user;
        }
        return false;
    }

    public function getAuthKey()
    {
        return ($this->getUser()) ? $this->getUser()->getId() : $this->getAuth('session');
    }

    public static function getAliases()
    {
        return [
            'resolve' => self::class
        ];
    }
}
