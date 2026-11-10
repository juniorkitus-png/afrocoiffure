<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_REVIEW_AUTHOR_HAIRDRESSER', fields: ['author', 'hairdresser'])]
#[ORM\Index(name: 'IDX_REVIEW_STATUS', fields: ['status'])]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['author', 'hairdresser'], errorPath: 'comment', message: 'Vous avez déjà laissé un avis pour ce coiffeur. Vous pouvez le modifier.')]
class Review
{
    // Modération (RM-04, RM-07)
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUSES = [
        'En attente' => self::STATUS_PENDING,
        'Approuvé' => self::STATUS_APPROVED,
        'Rejeté' => self::STATUS_REJECTED,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'smallint')]
    #[Assert\NotBlank(message: 'Choisissez une note.')]
    #[Assert\Range(min: 1, max: 5, notInRangeMessage: 'La note doit être comprise entre {{ min }} et {{ max }}.')]
    private ?int $rating = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'Le commentaire est obligatoire.')]
    #[Assert\Length(
        min: 10,
        max: 1000,
        minMessage: 'Votre avis doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Votre avis ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $comment = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(choices: [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_REJECTED])]
    private string $status = self::STATUS_PENDING;

    #[ORM\Column]
    private bool $isReported = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $author = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Hairdresser $hairdresser = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * RM-06 : un coiffeur ne peut pas noter sa propre fiche.
     */
    #[Assert\Callback]
    public function validateNotOwnProfile(ExecutionContextInterface $context): void
    {
        if ($this->author !== null && $this->hairdresser !== null
            && $this->author->getHairdresser() === $this->hairdresser) {
            $context->buildViolation('Vous ne pouvez pas laisser un avis sur votre propre fiche.')
                ->atPath('comment')
                ->addViolation();
        }
    }

    /**
     * RM-04 : à appeler dans le contrôleur quand l'AUTEUR modifie son avis
     * (et non quand l'admin le modère) → l'avis repasse en modération.
     */
    public function markAsEdited(): static
    {
        $this->status = self::STATUS_PENDING;

        return $this;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function getStatusLabel(): string
    {
        return array_search($this->status, self::STATUSES, true) ?: $this->status;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function isReported(): bool
    {
        return $this->isReported;
    }

    public function setIsReported(bool $isReported): static
    {
        $this->isReported = $isReported;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getHairdresser(): ?Hairdresser
    {
        return $this->hairdresser;
    }

    public function setHairdresser(?Hairdresser $hairdresser): static
    {
        $this->hairdresser = $hairdresser;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('%d/5 – %s', $this->rating, mb_strimwidth((string) $this->comment, 0, 40, '…'));
    }
}