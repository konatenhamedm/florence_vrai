<?php

namespace App\Entity;

use App\Repository\SalleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SalleRepository::class)
 */
class Salle
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\OneToMany(targetEntity=ElementSalle::class, mappedBy="salle" ,cascade={"persist"})
     */
    private $elementSalles;

    public function __construct()
    {
        $this->elementSalles = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * @return Collection<int, ElementSalle>
     */
    public function getElementSalles(): Collection
    {
        return $this->elementSalles;
    }

    public function addElementSalle(ElementSalle $elementSalle): self
    {
        if (!$this->elementSalles->contains($elementSalle)) {
            $this->elementSalles[] = $elementSalle;
            $elementSalle->setSalle($this);
        }

        return $this;
    }

    public function removeElementSalle(ElementSalle $elementSalle): self
    {
        if ($this->elementSalles->removeElement($elementSalle)) {
            // set the owning side to null (unless already changed)
            if ($elementSalle->getSalle() === $this) {
                $elementSalle->setSalle(null);
            }
        }

        return $this;
    }

}
