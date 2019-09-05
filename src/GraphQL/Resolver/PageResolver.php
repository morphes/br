<?php
namespace App\GraphQL\Resolver;

use Doctrine\ORM\EntityManager;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class PageResolver implements ResolverInterface, AliasedInterface {

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
        $pageUrl = $this->em
            ->getRepository('App:PageUrl')
            ->findByUrl($args['slug'] . '/');

        if($pageUrl) {
            return $pageUrl->getEntity();
        }

        return [];
    }

    /**
     * @return array
     */
    public static function getAliases()
    {
        return [
            'resolve' => 'Page'
        ];
    }
}