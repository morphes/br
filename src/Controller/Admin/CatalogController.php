<?php

namespace App\Controller\Admin;

use App\Entity\CatalogUrl;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use App\Service\AdminTagService;
use App\Entity\Catalog;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;

class CatalogController extends BaseAdminController
{
    private $tagService;

    private $entityManager;

    public function __construct(
        AdminTagService $tagService,
        EntityManager $entityManager
    ) {
        $this->tagService = $tagService;
        $this->entityManager = $entityManager;
    }

    /**
     * The method that is executed when the user performs a 'edit' action on an entity.
     *
     * @return Response|RedirectResponse
     */
    protected function editAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_EDIT);

        $id = $this->request->query->get('id');
        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];

        if($tags = $this->tagService->parseRequest($this->request->request->all())) {
            $this->tagService
                ->setTags($tags)
                ->setEntityType(Catalog::class)
                ->setEntity($entity)
                ->update();
        }

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
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->dispatch(EasyAdminEvents::PRE_UPDATE, array('entity' => $entity));

            $this->executeDynamicMethod('preUpdate<EntityName>Entity', array($entity, true));
            $this->executeDynamicMethod('update<EntityName>Entity', array($entity, $editForm));

            $this->dispatch(EasyAdminEvents::POST_UPDATE, array('entity' => $entity));

            return $this->redirectToReferrer();
        }

        $this->dispatch(EasyAdminEvents::POST_EDIT);

        $parameters = array(
            'form' => $editForm->createView(),
            'entity_fields' => $fields,
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );

        return $this->executeDynamicMethod('render<EntityName>Template', array('edit', $this->entity['templates']['edit'], $parameters));
    }

    protected function addUrlAction()
    {
        $url = $this->request->request->get('url');

        $checkUrl = $this->entityManager->getRepository('App:CatalogUrl')->findOneBy(
            ['url' => $url]
        );

        if($checkUrl) {
            $id = $checkUrl->getId();
        } else {
            $catalogUrl = new CatalogUrl();
            $catalogUrl->setUrl($url);

            $this->entityManager->persist($catalogUrl);
            $this->entityManager->flush();

            $id = $catalogUrl->getId();
        }


        $response = new Response();
        $response->setContent(json_encode([
            'id' => $id
        ]));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
