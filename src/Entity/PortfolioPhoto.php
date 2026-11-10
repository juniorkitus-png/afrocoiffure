<?php

namespace App\Entity;

use App\Repository\PortfolioPhotoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PortfolioPhotoRepository::class)]
#[ORM\Index(name: 'IDX_PHOTO_REPORTED', fields: ['isReported'])]
class PortfolioPhoto
{
    // Dossier public où sont stockées les images (public/uploads/portfolio)
    public const UPLOAD_DIR = 'uploads/portfolio';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Nom de fichier régénéré à l'upload (ex. 6731f0c2a9e4b.webp), jamais le nom d'origine
    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    #[ORM\Column(length: 150, nullable: true)]
    #[Assert\Length(max: 150, maxMessage: 'La légende ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $caption = null;

    #[ORM\Column]
    private bool $isReported = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'portfolioPhotos')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Hairdresser $hairdresser = null;

    // Facultatif : si la spécialité est supprimée, la photo reste mais sans spécialité
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Speciality $speciality = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * Chemin public de l'image, à utiliser dans Twig : {{ asset(photo.path) }}
     */
    public function getPath(): string
    {
        return self::UPLOAD_DIR . '/' . $this->filename;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setCaption(?string $caption): static
    {
        $this->caption = $caption;

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

    public function getHairdresser(): ?Hairdresser
    {
        return $this->hairdresser;
    }

    public function setHairdresser(?Hairdresser $hairdresser): static
    {
        $this->hairdresser = $hairdresser;

        return $this;
    }

    public function getSpeciality(): ?Speciality
    {
        return $this->speciality;
    }

    public function setSpeciality(?Speciality $speciality): static
    {
        $this->speciality = $speciality;

        return $this;
    }

    public function __toString(): string
    {
        return $this->caption ?? (string) $this->filename;
    }
}