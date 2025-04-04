<?php

namespace App\Entity;

use App\Repository\RequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\RequestType; 
use App\Entity\Person; 
use App\Entity\Department; 

#[ORM\Entity(repositoryClass: RequestRepository::class)]
class Request
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: RequestType::class, inversedBy: 'requests', cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    private ?RequestType $request_type;

    #[ORM\ManyToOne(targetEntity: Person::class, inversedBy: 'requests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Person $collaborator = null;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Department $department = null;

    #[ORM\Column]
    private ?\DateTime $created_at = null;
    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    #[ORM\Column]
    private ?\DateTime $start_at = null;

    #[ORM\Column]
    private ?\DateTime $end_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $receipt_file = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $answer_comment = null;

    #[ORM\Column(nullable: true)]
    private ?bool $answer = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $answer_at = null;

    public ?int $totalnbdemande = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequestType(): ?RequestType
    {
        return $this->request_type;
    }

    public function setRequestType(?RequestType $request_type): self
    {
        $this->request_type = $request_type;
        return $this;
    }

    public function getCollaborator(): ?Person
    {
        return $this->collaborator;
    }

    public function setCollaborator(?Person $collaborator): self
    {
        $this->collaborator = $collaborator;
        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): self
    {
        $this->department = $department;
        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAtValue(): void
{
    if ($this->created_at === null) {
        $this->created_at = new \DateTime();
    }
}

    public function getStartAt(): ?\DateTime
    {
        return $this->start_at;
    }

    public function setStartAt(\DateTime $start_at): static
    {
        $this->start_at = $start_at;

        return $this;
    }

    public function getEndAt(): ?\DateTime
    {
        return $this->end_at;
    }

    public function setEndAt(\DateTime $end_at): static
    {
        $this->end_at = $end_at;

        return $this;
    }

    public function getReceiptFile(): ?string
    {
        return $this->receipt_file;
    }

    public function setReceiptFile(?string $receipt_file): static
    {
        $this->receipt_file = $receipt_file;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getAnswerComment(): ?string
    {
        return $this->answer_comment;
    }

    public function setAnswerComment(?string $answer_comment): static
    {
        $this->answer_comment = $answer_comment;

        return $this;
    }

    public function isAnswer(): ?bool
    {
        return $this->answer;
    }

    public function setAnswer(?bool $answer): static
    {
        $this->answer = $answer;

        return $this;
    }

    public function getAnswerAt(): ?\DateTime
    {
        return $this->answer_at;
    }

    public function setAnswerAt(\DateTime $answer_at): static
    {
        $this->answer_at = $answer_at;

        return $this;
    }

    public function getTotalNbDemande(): int
    {
        // Logique pour calculer totalnbdemande
        return $this->totalnbdemande; // ou la valeur calculée
    }

}
