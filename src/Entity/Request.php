<?php

namespace App\Entity;

use App\Repository\RequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RequestRepository::class)]
class Request
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: RequestType::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?RequestType $request_type_id = null ;

    #[ORM\ManyToOne(targetEntity: Person::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Person $collaborator_id = null ;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Department $department_id = null ;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $start_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $end_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $receipt_file = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $answer_comment = null;

    #[ORM\Column(nullable: true)]
    private ?bool $answer = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $answer_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequestTypeId(): ?int
    {
        return $this->request_type_id;
    }

    public function setRequestTypeId(?int $request_type_id): static
    {
        $this->request_type_id = $request_type_id;

        return $this;
    }

    public function getCollaboratorId(): ?int
    {
        return $this->collaborator_id;
    }

    public function setCollaboratorId(?int $collaborator_id): static
    {
        $this->collaborator_id = $collaborator_id;

        return $this;
    }

    public function getDepartmentId(): ?int
    {
        return $this->department_id;
    }

    public function setDepartmentId(?int $department_id): static
    {
        $this->department_id = $department_id;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getStartAt(): ?\DateTimeImmutable
    {
        return $this->start_at;
    }

    public function setStartAt(\DateTimeImmutable $start_at): static
    {
        $this->start_at = $start_at;

        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->end_at;
    }

    public function setEndAt(\DateTimeImmutable $end_at): static
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

    public function getAnswerAt(): ?\DateTimeImmutable
    {
        return $this->answer_at;
    }

    public function setAnswerAt(\DateTimeImmutable $answer_at): static
    {
        $this->answer_at = $answer_at;

        return $this;
    }
}
