<?php

namespace App\Service\Manager;

use App\Entity\Product;
use App\Entity\ProductTagItem;
use App\Repository\CatalogRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductTagItemRepository;
use App\Repository\ProductTagRepository;
use App\Service\ConfigService;
use App\Service\DoctrineService;
use Doctrine\ORM\EntityManager;
use Redis;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Common\Persistence\ObjectManager;

class TagManager extends AbstractController
{
    private $redis;

    private $doctrineService;

    /**
     * @var ProductTagItemRepository
     */
    private $productTagItemRepository;
    /**
     * @var ProductTagRepository
     */
    private $productTagRepository;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var CatalogRepository
     */
    private $catalogRepository;

    /**
     * TagManager constructor.
     *
     * @param Redis $redis
     * @param EntityManager $em
     * @param DoctrineService $doctrineService
     * @param ConfigService $configService
     * @param ObjectManager $manager
     * @param ProductTagItemRepository $productTagItemRepository
     * @param ProductTagRepository $productTagRepository
     * @param ProductRepository $productRepository
     * @param CatalogRepository $catalogRepository
     */
    public function __construct(
        Redis $redis,
        EntityManager $em,
        DoctrineService $doctrineService,
        ConfigService $configService,
        ObjectManager $manager,
        ProductTagItemRepository $productTagItemRepository,
        ProductTagRepository $productTagRepository,
        ProductRepository $productRepository,
        CatalogRepository $catalogRepository
    ) {
        $this->em              = $em;
        $this->redis           = $redis;
        $this->doctrineService = $doctrineService;
        $this->configService   = $configService;
        $this->manager         = $manager;
        $this->productTagItemRepository = $productTagItemRepository;
        $this->productTagRepository = $productTagRepository;
        $this->productRepository = $productRepository;
        $this->catalogRepository = $catalogRepository;
    }

    /**
     * @return array|mixed|object
     */
    public function getFilters()
    {
        $method   = 'get' . $this->getEntityType() . 'Filters';
        $cacheKey = $method . $this->getEntity()->getId();

//        $cacheItem = json_decode($this->redis->get($cacheKey));
//        if (!$cacheItem) {
        if (method_exists($this, $method)) {
            $cacheItem = $this->$method();
            $this->redis->set($cacheKey, json_encode($cacheItem));
            return $cacheItem;
        }
//        }
//        return $cacheItem;
    }

    /**
     * @return array
     */
    public function getCatalogFilters()
    {
        $allTags = $this->productTagRepository->findAll();

        $tags        = [];
        $productTags = [];

        foreach ($this->getEntity()->getProducts() as $product) {
            foreach ($product->getProducttagitem() as $productTag) {
                $tagId = $productTag->getId();
                if (isset($productTags[$tagId])) {
                    $productTags[$tagId] += 1;
                } else {
                    $productTags[$tagId] = 1;
                }
            }
        }
        foreach ($allTags as $allTag) {
            $tag          = [
                'name' => $allTag->getName(),
                'id'   => $allTag->getId()
            ];
            $tagChildrens = [];
            foreach ($allTag->getProductTagItems() as $productTagItem) {
                $count = 0;
                if (isset($productTags[$productTagItem->getId()])) {
                    $count = $productTags[$productTagItem->getId()];
                }
                if($count) {
                    $tagChildrens[] = [
                        'name'  => $productTagItem->getName(),
                        'id'    => $productTagItem->getId(),
                        'count' => $count
                    ];
                }
            }
            $tag['childrens'] = $tagChildrens;
            $tags[]           = $tag;
        }
        return $tags;
    }

    /**
     * @return array
     */
    public function getProductFilters()
    {
        $productTags = [];
        foreach ($this->getEntity()->getProducttagitem() as $productTag) {
            $productTags[] = $productTag->getId();
        }

        $tagsItems = $this->productTagItemRepository->findBy(['id' => $productTags], ['id' => 'DESC']);

        $tags = [];
        foreach ($tagsItems as $tag) {
            if ($tag->getName()) {
                $tagEntity = $tag->getEntityId();
                $tagEntity->setCurrentLocale($this->getLocale());
                $tags[] = [
                    'name'  => $tagEntity->getName(),
                    'value' => $tag->getName()
                ];
            }
        }

        return $tags;
    }

    /**
     * @return array
     */
    public function all()
    {
        $result = [];

        $tags   = $this->productTagRepository->findAll();

        foreach ($tags as $tag) {
            $result[] = [
                'name' => $tag->getName(),
                'id'   => $tag->getId()
            ];
        }
        return $result;
    }

    /**
     * @return mixed
     */
    public function getOne()
    {
        if ($this->getEntity()) {
            $method = 'getOneFor' . $this->getEntityType();
            return $this->$method();
        }
    }

    /**
     * @return mixed
     */
    public function getOneForProduct()
    {
        return $this->getEntity()->getProducttagitem()->filter(function (ProductTagItem $productTagItem) {
            return $productTagItem->getEntityId()->getId() == $this->getTagId();
        }
        )->first();
    }

    /**
     * @return mixed
     */
    public function getOneForCatalog()
    {
        return $this->catalogRepository->findByTagId($this->getTagId());
    }

    /**
     * @return mixed
     */
    public function getMultiple()
    {
        if ($this->getEntity()) {
            $method = 'getMultipleFor' . $this->getEntityType();
            return $this->$method();
        }
    }

    /**
     * @return mixed
     */
    public function getMultipleForProduct()
    {
        return $this->getEntity()->getProducttagitem()->filter(function (ProductTagItem $productTagItem) {
            return in_array($productTagItem->getEntityId()->getId(), $this->getTagsIds());
        }
        );
    }

    /**
     * @return mixed
     */
    public function similars()
    {
        $productsTagIds = $this->_getProductTagsIds();

        $tagsItems            = $this->setEntityType(Product::class)
            ->setEntity($this->getEntity())
            ->setTagsIds($productsTagIds)
            ->getMultiple();
        $productsTagsItemsIds = [];

        foreach ($tagsItems as $tagsItem) {
            if (in_array($tagsItem->getEntityId()->getId(), $productsTagIds)) {
                $productsTagsItemsIds[] = $tagsItem->getId();
            }
        }

        return $this->setTagsIds($productsTagsItemsIds)
            ->getProducts();
    }

    /**
     * @return array
     */
    private function _getExtraTagsIds()
    {
        return explode(
            ',', $this->configService->get('extra_tags_similar_products')
        );
    }

    /**
     * @return array
     */
    private function _getProductTagsIds()
    {
        return explode(
            ',', $this->configService->get('required_tags_similar_products')
        );
    }

    /**
     * @return array|\object[]
     */
    public function getProducts()
    {
        if (!$this->getTagsIds()) {
            return [];
        }

        $productsIds = $this->productTagItemRepository->getProducts($this->getTagsIds());

        $categoriesProductsIds = [];
        foreach ($this->getEntity()->getCatalog() as $catalog) {
            foreach ($catalog->getProducts() as $product) {
                $categoriesProductsIds[] = $product->getId();
            }
        }

        $productsIds = array_intersect($productsIds, $categoriesProductsIds);

        return $this->productRepository->findBy(['id' => $productsIds]);
    }

    /**
     * @return array|\object[]
     */
    public function getCatalogs()
    {
        return $this->catalogRepository->getAllByParentTag($this->getParentTagId());
    }
}