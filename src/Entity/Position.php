<?php

namespace App\Entity;

use App\Repository\PositionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: PositionRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'Ce poste existe déjà.')] 
class Position
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom ne peut pas être vide.")]
    #[Assert\Length(max: 255, maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'position', targetEntity: Person::class, cascade: ["persist", "remove"])]
    private Collection $persons;

    public function __construct() 
    {
        $this->persons = new ArrayCollection();
    }

    public function getPerson(): Collection
    {
        return $this->persons;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getTotalNbDemande(): int
    {
        return count($this->persons);
    }
}
