<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RegionRepository")
 */
class Region
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
    private $title;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\City", mappedBy="region", cascade={"remove"})
     */
    private $cities;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $kladr_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fias_id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserAddress", mappedBy="region")
     */
    private $userAddresses;

    public function __construct()
    {
        $this->cities = new ArrayCollection();
        $this->userAddresses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection|City[]
     */
    public function getCities(): Collection
    {
        return $this->cities;
    }

    public function addCity(City $city): self
    {
        if (!$this->cities->contains($city)) {
            $this->cities[] = $city;
            $city->setRegion($this);
        }

        return $this;
    }

    public function removeCity(City $city): self
    {
        if ($this->cities->contains($city)) {
            $this->cities->removeElement($city);
            // set the owning side to null (unless already changed)
            if ($city->getRegion() === $this) {
                $city->setRegion(null);
            }
        }

        return $this;
    }

    public function getKladrId(): ?string
    {
        return $this->kladr_id;
    }

    public function setKladrId(?string $kladr_id): self
    {
        $this->kladr_id = $kladr_id;

        return $this;
    }

    public function getFiasId(): ?string
    {
        return $this->fias_id;
    }

    public function setFiasId(?string $fias_id): self
    {
        $this->fias_id = $fias_id;

        return $this;
    }

    /**
     * @return Collection|UserAddress[]
     */
    public function getUserAddresses(): Collection
    {
        return $this->userAddresses;
    }

    public function addUserAddress(UserAddress $userAddress): self
    {
        if (!$this->userAddresses->contains($userAddress)) {
            $this->userAddresses[] = $userAddress;
            $userAddress->setRegion($this);
        }

        return $this;
    }

    public function removeUserAddress(UserAddress $userAddress): self
    {
        if ($this->userAddresses->contains($userAddress)) {
            $this->userAddresses->removeElement($userAddress);
            // set the owning side to null (unless already changed)
            if ($userAddress->getRegion() === $this) {
                $userAddress->setRegion(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return self::class;
    }

    public function __call($method, $arguments)
    {
        $method = ('get' === substr($method, 0, 3) || 'set' === substr($method, 0, 3)) ? $method : 'get'. ucfirst($method);

        return $this->proxyCurrentLocaleTranslation($method, $arguments);
    }

    public function __get($name)
    {
        $method = 'get' . implode(array_map('ucfirst', explode('_', $name)));
        $arguments = [];
        return $this->proxyCurrentLocaleTranslation($method, $arguments);
    }
}
