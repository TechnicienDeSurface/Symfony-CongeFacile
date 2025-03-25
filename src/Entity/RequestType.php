<?php

namespace App\Entity;

use App\Repository\RequestTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RequestTypeRepository::class)] class RequestType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'request_type', targetEntity: Request::class, cascade: ["persist", "remove"])]
    private Collection $requests;

    public function __construct()
    {
        $this->requests = new ArrayCollection();
    }

    public function getRequests(): Collection
    {
        return $this->requests;
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
        return count($this->requests);
    }

}
