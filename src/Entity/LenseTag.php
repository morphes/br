<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LenseTagRepository")
 */
class LenseTag
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $visible;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LenseItemTag", mappedBy="entity")
     */
    private $lenseItemTags;

    public function __construct()
    {
        $this->lenseItemTags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(?\DateTimeInterface $created): self
    {
        $this->created = $created;

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

    /**
     * @return Collection|LenseItemTag[]
     */
    public function getLenseItemTags(): Collection
    {
        return $this->lenseItemTags;
    }

    public function addLenseItemTag(LenseItemTag $lenseItemTag): self
    {
        if (!$this->lenseItemTags->contains($lenseItemTag)) {
            $this->lenseItemTags[] = $lenseItemTag;
            $lenseItemTag->setEntity($this);
        }

        return $this;
    }

    public function removeLenseItemTag(LenseItemTag $lenseItemTag): self
    {
        if ($this->lenseItemTags->contains($lenseItemTag)) {
            $this->lenseItemTags->removeElement($lenseItemTag);
            // set the owning side to null (unless already changed)
            if ($lenseItemTag->getEntity() === $this) {
                $lenseItemTag->setEntity(null);
            }
        }

        return $this;
    }
}
