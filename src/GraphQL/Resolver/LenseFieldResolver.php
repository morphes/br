<?php

namespace App\GraphQL\Resolver;

use App\Service\LenseService;
use App\Service\Twig\Lenses;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use App\Entity\Lense;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;
use Overblog\GraphQLBundle\Relay\Connection\Paginator;
use Overblog\GraphQLBundle\Relay\Connection\Output\Connection;
use App\Service\Twig\LenseTags;

class LenseFieldResolver extends LocaleAlias
{
    private $lenseTagService;
    /**
     * @var Lenses
     */
    private $lensesService;

    /**
     * LenseFieldResolver constructor.
     *
     * @param LenseTags $lenseTags
     * @param Lenses $lensesService
     */
    public function __construct(
        LenseTags $lenseTags,
        Lenses $lensesService
    ) {
        $this->lenseTagService = $lenseTags;
        $this->lensesService = $lensesService;
    }

    /**
     * @param ResolveInfo $info
     * @param $value
     * @param Argument $args
     * @return mixed
     */
    public function __invoke(ResolveInfo $info, $value, Argument $args)
    {
        $method = $info->fieldName;
        return $this->$method($value, $args);
    }

    /**
     * @param Lense $lense
     * @return mixed
     */
    public function id(Lense $lense)
    {
        return $lense->getId();
    }
    
    /**
     * @param Lense $lense
     * @return mixed
     */
    public function name(Lense $lense)
    {
        return $lense->getName();
    }

    /**
     * @param Lense $lense
     * @return mixed
     */
    public function price(Lense $lense)
    {
        return $lense->getPrice();
    }

    /**
     * @param Lense $lense
     * @return bool|null
     */
    public function is_recipe(Lense $lense)
    {
        return boolval($lense->getIsRecipe());
    }

    public function lenseitemstags(Lense $lense)
    {
        return $lense->getLensesTagsCollection();
    }

    public function recipes(Lense $lense)
    {
        if(!$lense || $this->lensesService->isNonReceipt($lense)) {
            return [];
        }
        return $this->lenseTagService->getLenseTagsItemsTree('receipt');
    }

    /**
     * @return array
     */
    public static function getAliases()
    {
        return [
            'resolve' => 'LenseField'
        ];
    }
}