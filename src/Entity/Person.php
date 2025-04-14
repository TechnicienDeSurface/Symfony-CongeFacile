<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Department;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $last_name = null;

    #[ORM\Column(length: 100)]
    private ?string $first_name = null;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: true)] //Il faut rendre le manager_id nullable sinon on ne peut pas créer de person et de user 
    private ?User $manager = null;

    #[ORM\ManyToOne(targetEntity: Department::class, cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Department $department = null;

    #[ORM\ManyToOne(targetEntity: Position::class, inversedBy: 'persons', cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Position $position = null;

    #[ORM\OneToOne(mappedBy: 'person', targetEntity: User::class, cascade: ["persist"])]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'collaborator', targetEntity: Request::class)]
    private Collection $requests;

    public function __construct()
    {
        $this->requests = new ArrayCollection();
    }

    #[ORM\Column]
    private ?bool $alert_new_request = null;

    #[ORM\Column]
    private ?bool $alert_on_answer = null;

    #[ORM\Column]
    private ?bool $alert_before_vacation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getManagerId(): ?User
    {
        return $this->manager;
    }

    public function setManager(?User $manager): static
    {
        $this->manager = $manager;

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function getDepartmentId(): ?int
    {
        return $this->department ? $this->department->getId() : null;
    }

    public function setDepartment(?Department $department): static
    {
        $this->department = $department;

        return $this;
    }

    public function getPositionId(): ?Position
    {
        return $this->position;
    }

    public function setPosition(?Position $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function isAlertNewRequest(): ?bool
    {
        return $this->alert_new_request;
    }

    public function setAlertNewRequest(bool $alert_new_request): static
    {
        $this->alert_new_request = $alert_new_request;

        return $this;
    }

    public function isAlertOnAnswer(): ?bool
    {
        return $this->alert_on_answer;
    }

    public function setAlertOnAnswer(bool $alert_on_answer): static
    {
        $this->alert_on_answer = $alert_on_answer;

        return $this;
    }

    public function isAlertBeforeVacation(): ?bool
    {
        return $this->alert_before_vacation;
    }

    public function setAlertBeforeVacation(bool $alert_before_vacation): static
    {
        $this->alert_before_vacation = $alert_before_vacation;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getFirstNameLastName(): string
    {
        return $this->first_name . " " . $this->last_name;
    }

    //METHODE POUR RECUPERER LES CONGES
    public function getRequests(): Collection
    {
        return $this->requests;
    }

    //FONCTION QUI CALCULE LES JOURS RESTANTS
    public function getTotalLeaveDays(): int
    {
        $totalDays = 0;

        foreach ($this->requests as $request) {
            if ($request->getStartAt() && $request->getEndAt()) {
                $startDate = $request->getStartAt();
                $endDate = $request->getEndAt();
                $currentDate = clone $startDate;

                // Calculer le nombre total de jours en excluant les weekends
                while ($currentDate <= $endDate) {
                    // Si le jour est un samedi (6) ou un dimanche (0), ne pas l'ajouter
                    if ($currentDate->format('N') < 6) {
                        $totalDays++;
                    }

                    $currentDate->modify('+1 day');
                }
            }
        }

        return $totalDays;
    }

    public function getRoles(): array
    {
        return $this->user ? $this->user->getRoles() : [];
    }
}
