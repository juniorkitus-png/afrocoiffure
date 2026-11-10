<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: 'Le nom de la prestation est obligatoire.')]
    #[Assert\Length(max: 150, maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $name = null;

    // Doctrine renvoie les colonnes DECIMAL sous forme de chaîne (précision exacte, pas d'arrondi float)
    #[ORM\Column(type: 'decimal', precision: 6, scale: 2)]
    #[Assert\NotBlank(message: 'Le prix est obligatoire.')]
    #[Assert\Positive(message: 'Le prix doit être supérieur à 0 €.')]
    #[Assert\LessThan(value: 10000, message: 'Le prix doit être inférieur à 10 000 €.')]
    private ?string $price = null;

    #[ORM\Column(type: 'smallint')]
    #[Assert\NotBlank(message: 'La durée est obligatoire.')]
    #[Assert\Range(min: 5, max: 720, notInRangeMessage: 'La durée doit être comprise entre {{ min }} et {{ max }} minutes.')]
    private ?int $durationMinutes = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(max: 1000)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'services')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Hairdresser $hairdresser = null;

    // Pas de cascade : une spécialité utilisée ne peut pas être supprimée (RM-14)
    #[ORM\ManyToOne(inversedBy: 'services')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Choisissez une spécialité.')]
    private ?Speciality $speciality = null;

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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Durée lisible : 90 → « 1 h 30 », 45 → « 45 min ».
     */
    public function getDurationLabel(): string
    {
        $h = intdiv((int) $this->durationMinutes, 60);
        $m = (int) $this->durationMinutes % 60;

        if ($h === 0) {
            return $m . ' min';
        }

        return $m === 0 ? $h . ' h' : sprintf('%d h %02d', $h, $m);
    }

    public function getDurationMinutes(): ?int
    {
        return $this->durationMinutes;
    }

    public function setDurationMinutes(int $durationMinutes): static
    {
        $this->durationMinutes = $durationMinutes;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

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
        return sprintf('%s (%s €)', $this->name, $this->price);
    }
}