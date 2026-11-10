<?php

namespace App\Entity;

use App\Repository\ContactRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContactRequestRepository::class)]
#[ORM\Index(name: 'IDX_CONTACT_STATUS', fields: ['status'])]
class ContactRequest
{
    // RM-09 : nouvelle → acceptée ou refusée par le coiffeur
    public const STATUS_NEW = 'new';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REFUSED = 'refused';
    public const STATUSES = [
        'Nouvelle' => self::STATUS_NEW,
        'Acceptée' => self::STATUS_ACCEPTED,
        'Refusée' => self::STATUS_REFUSED,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Votre nom est obligatoire.')]
    #[Assert\Length(max: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: 'Votre adresse e-mail est obligatoire.')]
    #[Assert\Email(message: 'Cette adresse e-mail n\'est pas valide.')]
    private ?string $email = null;

    #[ORM\Column(length: 30, nullable: true)]
    #[Assert\Length(max: 30)]
    private ?string $phone = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'Le message est obligatoire.')]
    #[Assert\Length(min: 10, max: 2000, minMessage: 'Votre message doit contenir au moins {{ limit }} caractères.')]
    private ?string $message = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    #[Assert\GreaterThan('today', message: 'La date souhaitée doit être dans le futur.')]
    private ?\DateTimeImmutable $desiredDate = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(choices: [self::STATUS_NEW, self::STATUS_ACCEPTED, self::STATUS_REFUSED])]
    private string $status = self::STATUS_NEW;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'contactRequests')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Hairdresser $hairdresser = null;

    // Null si envoyée par un visiteur, ou si le compte du client a été supprimé (RM-13)
    #[ORM\ManyToOne(inversedBy: 'contactRequests')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $sender = null;

    // Prestation souhaitée, facultative
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Service $service = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function isNew(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    public function getStatusLabel(): string
    {
        return array_search($this->status, self::STATUSES, true) ?: $this->status;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getDesiredDate(): ?\DateTimeImmutable
    {
        return $this->desiredDate;
    }

    public function setDesiredDate(?\DateTimeImmutable $desiredDate): static
    {
        $this->desiredDate = $desiredDate;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

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

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): static
    {
        $this->service = $service;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('Demande de %s (%s)', $this->name, $this->getStatusLabel());
    }
}