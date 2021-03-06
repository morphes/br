<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LandingBlockRepository")
 */
class LandingBlock
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $prefix;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $suffix;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $visibility;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\LandingBlock", inversedBy="landingBlocks")
     */
    private $childrens;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\LandingBlock", mappedBy="childrens")
     */
    private $landingBlocks;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Lense", inversedBy="landingBlocks")
     */
    private $tests;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\LandingPage", mappedBy="blocks")
     */
    private $landingPages;

    public function __construct()
    {
        $this->childrens = new ArrayCollection();
        $this->landingBlocks = new ArrayCollection();
        $this->tests = new ArrayCollection();
        $this->landingPages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function setPrefix(?string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function getSuffix(): ?string
    {
        return $this->suffix;
    }

    public function setSuffix(?string $suffix): self
    {
        $this->suffix = $suffix;

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

    public function getVisibility(): ?bool
    {
        return $this->visibility;
    }

    public function setVisibility(?bool $visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function __toString()
    {
        return self::class;
    }

    /**
     * @return Collection|self[]
     */
    public function getChildrens(): Collection
    {
        return $this->childrens;
    }

    public function addChildren(self $children): self
    {
        if (!$this->childrens->contains($children)) {
            $this->childrens[] = $children;
        }

        return $this;
    }

    public function removeChildren(self $children): self
    {
        if ($this->childrens->contains($children)) {
            $this->childrens->removeElement($children);
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getLandingBlocks(): Collection
    {
        return $this->landingBlocks;
    }

    public function addLandingBlock(self $landingBlock): self
    {
        if (!$this->landingBlocks->contains($landingBlock)) {
            $this->landingBlocks[] = $landingBlock;
            $landingBlock->addChildren($this);
        }

        return $this;
    }

    public function removeLandingBlock(self $landingBlock): self
    {
        if ($this->landingBlocks->contains($landingBlock)) {
            $this->landingBlocks->removeElement($landingBlock);
            $landingBlock->removeChildren($this);
        }

        return $this;
    }

    /**
     * @return Collection|Lense[]
     */
    public function getTests(): Collection
    {
        return $this->tests;
    }

    public function addTest(Lense $test): self
    {
        if (!$this->tests->contains($test)) {
            $this->tests[] = $test;
        }

        return $this;
    }

    public function removeTest(Lense $test): self
    {
        if ($this->tests->contains($test)) {
            $this->tests->removeElement($test);
        }

        return $this;
    }

    /**
     * @return Collection|LandingPage[]
     */
    public function getLandingPages(): Collection
    {
        return $this->landingPages;
    }

    public function addLandingPage(LandingPage $landingPage): self
    {
        if (!$this->landingPages->contains($landingPage)) {
            $this->landingPages[] = $landingPage;
            $landingPage->addBlock($this);
        }

        return $this;
    }

    public function removeLandingPage(LandingPage $landingPage): self
    {
        if ($this->landingPages->contains($landingPage)) {
            $this->landingPages->removeElement($landingPage);
            $landingPage->removeBlock($this);
        }

        return $this;
    }
}
