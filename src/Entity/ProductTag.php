<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductTagRepository")
 */
class ProductTag
{
    const VISIBLE_YES = 'Yes';

    const VISIBLE_NO = 'No';

    use ORMBehaviors\Translatable\Translatable;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Product", inversedBy="productTags")
     */
    private $entity_id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductTagItem", mappedBy="entity_id", cascade={"remove"})
     */
    private $productTagItems;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $visible;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Catalog", inversedBy="productTags")
     */
    private $catalog;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $input_type;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_on_product_page;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $sort_order;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $filterable;

    public function __construct()
    {
        $this->entity_id = new ArrayCollection();
        $this->productTagItems = new ArrayCollection();
        $this->created = new \DateTime('now');
        $this->catalog = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }
    
    /**
     * @return Collection|Product[]
     */
    public function getEntityId(): Collection
    {
        return $this->entity_id;
    }

    public function addEntityId(Product $entityId): self
    {
        if (!$this->entity_id->contains($entityId)) {
            $this->entity_id[] = $entityId;
        }

        return $this;
    }

    public function removeEntityId(Product $entityId): self
    {
        if ($this->entity_id->contains($entityId)) {
            $this->entity_id->removeElement($entityId);
        }

        return $this;
    }

    /**
     * @return Collection|ProductTagItem[]
     */
    public function getProductTagItems(): Collection
    {
        return $this->productTagItems;
    }

    public function addProductTagItem(ProductTagItem $productTagItem): self
    {
        if (!$this->productTagItems->contains($productTagItem)) {
            $this->productTagItems[] = $productTagItem;
            $productTagItem->setEntityId($this);
        }

        return $this;
    }

    public function removeProductTagItem(ProductTagItem $productTagItem): self
    {
        if ($this->productTagItems->contains($productTagItem)) {
            $this->productTagItems->removeElement($productTagItem);
            // set the owning side to null (unless already changed)
            if ($productTagItem->getEntityId() === $this) {
                $productTagItem->setEntityId(null);
            }
        }

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getVisible(): ?string
    {
        return $this->visible;
    }

    public function setVisible(?string $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    public function __toString()
    {
        return self::class;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Catalog[]
     */
    public function getCatalog(): Collection
    {
        return $this->catalog;
    }

    public function addCatalog(Catalog $catalog): self
    {
        if (!$this->catalog->contains($catalog)) {
            $this->catalog[] = $catalog;
        }

        return $this;
    }

    public function removeCatalog(Catalog $catalog): self
    {
        if ($this->catalog->contains($catalog)) {
            $this->catalog->removeElement($catalog);
        }

        return $this;
    }

    public function getInputType(): ?string
    {
        return $this->input_type;
    }

    public function setInputType(?string $input_type): self
    {
        $this->input_type = $input_type;

        return $this;
    }

    public function getIsOnProductPage(): ?bool
    {
        return $this->is_on_product_page;
    }

    public function setIsOnProductPage(?bool $is_on_product_page): self
    {
        $this->is_on_product_page = $is_on_product_page;

        return $this;
    }

    public function __call($method, $arguments)
    {
        $method = ('get' === substr($method, 0, 3) || 'set' === substr($method, 0, 3)) ? $method : 'get'. ucfirst($method);

        return $this->proxyCurrentLocaleTranslation($method, $arguments);
    }

    public function __get($name)
    {
        $method = 'get'. ucfirst($name);
        $arguments = [];
        return $this->proxyCurrentLocaleTranslation($method, $arguments);
    }

    public function getSortOrder(): ?int
    {
        return $this->sort_order;
    }

    public function setSortOrder(?int $sort_order): self
    {
        $this->sort_order = $sort_order;

        return $this;
    }

    public function getFilterable(): ?bool
    {
        return $this->filterable;
    }

    public function setFilterable(?bool $filterable): self
    {
        $this->filterable = $filterable;

        return $this;
    }
}
