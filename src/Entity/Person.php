<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Department;  

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

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $manager_id = null ;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Department $department_id = null ;

    #[ORM\ManyToOne(targetEntity: Position::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Position $position_id = null ;

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

    public function getManagerId(): ?int
    {
        return $this->manager_id;
    }

    public function setManagerId(int $manager_id): static
    {
        $this->manager_id = $manager_id;

        return $this;
    }

    public function getDepartmentId(): ?int
    {
        return $this->department_id;
    }

    public function setDepartmentId(int $department_id): static
    {
        $this->department_id = $department_id;

        return $this;
    }

    public function getPositionId(): ?int
    {
        return $this->position_id;
    }

    public function setPositionId(int $position_id): static
    {
        $this->position_id = $position_id;

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
}
