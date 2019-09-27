<?php
namespace App\GraphQL\Resolver;

use Doctrine\ORM\EntityManager;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class BlockResolver implements ResolverInterface, AliasedInterface {

    private $em;

    /**
     * PageResolver constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Argument $args
     * @return array
     */
    public function resolve(Argument $args)
    {
        return $this->em
            ->getRepository('App:Block')
            ->findByName($args['id']);
    }

    /**
     * @return array
     */
    public static function getAliases()
    {
        return [
            'resolve' => 'Block'
        ];
    }
}