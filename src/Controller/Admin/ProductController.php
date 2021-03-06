<?php

namespace App\Controller\Admin;

use App\Repository\ProductUrlRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use App\Service\AdminTagService;
use App\Entity\Product;
use App\Entity\ProductUrl;
use App\Service\ProductService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends BaseAdminController
{
    private $_template = 'admin/Product/edit.html.twig';

    private $tagService;

    private $productService;

    private $entityManager;
    /**
     * @var ProductUrlRepository
     */
    private $productUrlRepository;

    public function __construct(
        AdminTagService $tagService,
        ProductService $productService,
        EntityManager $entityManager,
        ProductUrlRepository $productUrlRepository
    ) {
        $this->tagService = $tagService;
        $this->productService = $productService;
        $this->entityManager = $entityManager;
        $this->productUrlRepository = $productUrlRepository;
    }

    protected function newAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_NEW);

        $entity = $this->executeDynamicMethod('createNew<EntityName>Entity');

        $easyadmin         = $this->request->attributes->get('easyadmin');
        $easyadmin['item'] = $entity;
        $this->request->attributes->set('easyadmin', $easyadmin);

        $fields = $this->entity['new']['fields'];

        $newForm = $this->executeDynamicMethod('create<EntityName>NewForm', array($entity, $fields));

        $newForm->handleRequest($this->request);
        if ($newForm->isSubmitted() && $newForm->isValid()) {
            $this->dispatch(EasyAdminEvents::PRE_PERSIST, array('entity' => $entity));

            $this->executeDynamicMethod('prePersist<EntityName>Entity', array($entity, true));
            $this->executeDynamicMethod('persist<EntityName>Entity', array($entity, $newForm));

            $this->dispatch(EasyAdminEvents::POST_PERSIST, array('entity' => $entity));
            if($tags = $this->tagService->parseRequest($this->request->request->all())) {
                $this->tagService
                    ->setTags($tags)
                    ->setEntityType(Product::class)
                    ->setEntity($entity)
                    ->update();
            }
            if($catalogsIds = $this->request->request->get('catalogs')) {
                $this->productService
                    ->setProduct($entity)
                    ->updateCatalogs($catalogsIds);
            }

            return $this->redirectToReferrer();
        }

        $this->dispatch(EasyAdminEvents::POST_NEW, array(
                'entity_fields' => $fields,
                'form'          => $newForm,
                'entity'        => $entity,
            )
        );

        $parameters = array(
            'form'          => $newForm->createView(),
            'entity_fields' => $fields,
            'entity'        => $entity,
        );

        return $this->executeDynamicMethod('render<EntityName>Template', array('new', $this->entity['templates']['new'], $parameters));
    }

    protected function editAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_EDIT);

        $id = $this->request->query->get('id');
        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];

        if ($this->request->isXmlHttpRequest() && $property = $this->request->query->get('property')) {
            $newValue = 'true' === mb_strtolower($this->request->query->get('newValue'));
            $fieldsMetadata = $this->entity['list']['fields'];

            if (!isset($fieldsMetadata[$property]) || 'toggle' !== $fieldsMetadata[$property]['dataType']) {
                throw new \RuntimeException(sprintf('The type of the "%s" property is not "toggle".', $property));
            }

            $this->updateEntityProperty($entity, $property, $newValue);

            // cast to integer instead of string to avoid sending empty responses for 'false'
            return new Response((int) $newValue);
        }

        $fields = $this->entity['edit']['fields'];

        $editForm = $this->executeDynamicMethod('create<EntityName>EditForm', array($entity, $fields));
        $deleteForm = $this->createDeleteForm($this->entity['name'], $id);

        $editForm->handleRequest($this->request);
        $productTags = [];
        $allProductsTags = [];
        foreach($entity->getProducttagitem() as $tagItem) {
            $allProductsTags[] = $tagItem->getId();
            $tagId = $tagItem->getEntityId()->getId();
            if(!isset($productTags[$tagId])) {
                $productTags[$tagId] = [$tagItem->getId()];
            } else {
                $productTags[$tagId] = array_merge($productTags[$tagId], [$tagItem->getId()]);
            }
        }
        foreach($productTags as $tagId => $tagValues) {
            $productTags[$tagId] = implode(',', $tagValues);
        }
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->dispatch(EasyAdminEvents::PRE_UPDATE, array('entity' => $entity));

            $this->executeDynamicMethod('preUpdate<EntityName>Entity', array($entity, true));
            $this->executeDynamicMethod('update<EntityName>Entity', array($entity, $editForm));

            $this->dispatch(EasyAdminEvents::POST_UPDATE, array('entity' => $entity));
            if($tags = $this->tagService->parseRequest($this->request->request->all())) {
                $this->tagService
                    ->setTags($tags)
                    ->setEntityType(Product::class)
                    ->setEntity($entity)
                    ->update();
            }
            if($catalogsIds = $this->request->request->get('catalogs')) {
                $this->productService
                    ->setProduct($entity)
                    ->updateCatalogs($catalogsIds);
            }
            return $this->redirectToReferrer();
        }

        $this->dispatch(EasyAdminEvents::POST_EDIT);
        $entity->setTagsArray($productTags);
        $entity->setAllTagsArray($allProductsTags);
        $parameters = array(
            'form' => $editForm->createView(),
            'entity_fields' => $fields,
            'entity' => $entity,
            'delete_form' => $deleteForm->createView()
        );

        return $this->executeDynamicMethod('render<EntityName>Template', array('edit', $this->_template, $parameters));
    }
}
