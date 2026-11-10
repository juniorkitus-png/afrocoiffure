<?php

namespace App\Entity;

use App\Repository\OpeningHoursRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: OpeningHoursRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_HAIRDRESSER_DAY', fields: ['hairdresser', 'dayOfWeek'])]
#[UniqueEntity(fields: ['hairdresser', 'dayOfWeek'], message: 'Ce jour est déjà renseigné.')]
class OpeningHours
{
    // Numérotation ISO-8601 : 1 = lundi … 7 = dimanche (comme date('N') en PHP)
    public const DAYS = [
        1 => 'Lundi',
        2 => 'Mardi',
        3 => 'Mercredi',
        4 => 'Jeudi',
        5 => 'Vendredi',
        6 => 'Samedi',
        7 => 'Dimanche',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'smallint')]
    #[Assert\Range(min: 1, max: 7, notInRangeMessage: 'Le jour doit être compris entre 1 (lundi) et 7 (dimanche).')]
    private ?int $dayOfWeek = null;

    #[ORM\Column(type: 'time_immutable', nullable: true)]
    private ?\DateTimeImmutable $openTime = null;

    #[ORM\Column(type: 'time_immutable', nullable: true)]
    private ?\DateTimeImmutable $closeTime = null;

    #[ORM\Column]
    private bool $isClosed = false;

    #[ORM\ManyToOne(inversedBy: 'openingHours')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Hairdresser $hairdresser = null;

    /**
     * Si le salon est ouvert ce jour-là, les deux heures sont obligatoires
     * et la fermeture doit être après l'ouverture.
     */
    #[Assert\Callback]
    public function validateHours(ExecutionContextInterface $context): void
    {
        if ($this->isClosed) {
            return;
        }

        if ($this->openTime === null || $this->closeTime === null) {
            $context->buildViolation('Indiquez l\'heure d\'ouverture et de fermeture, ou cochez « Fermé ».')
                ->atPath('openTime')
                ->addViolation();

            return;
        }

        if ($this->closeTime <= $this->openTime) {
            $context->buildViolation('L\'heure de fermeture doit être après l\'heure d\'ouverture.')
                ->atPath('closeTime')
                ->addViolation();
        }
    }

    public function getDayLabel(): string
    {
        return self::DAYS[$this->dayOfWeek] ?? '';
    }

    /**
     * Ex. « 09:00 – 18:00 » ou « Fermé ».
     */
    public function getHoursLabel(): string
    {
        if ($this->isClosed || $this->openTime === null || $this->closeTime === null) {
            return 'Fermé';
        }

        return $this->openTime->format('H:i') . ' – ' . $this->closeTime->format('H:i');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDayOfWeek(): ?int
    {
        return $this->dayOfWeek;
    }

    public function setDayOfWeek(int $dayOfWeek): static
    {
        $this->dayOfWeek = $dayOfWeek;

        return $this;
    }

    public function getOpenTime(): ?\DateTimeImmutable
    {
        return $this->openTime;
    }

    public function setOpenTime(?\DateTimeImmutable $openTime): static
    {
        $this->openTime = $openTime;

        return $this;
    }

    public function getCloseTime(): ?\DateTimeImmutable
    {
        return $this->closeTime;
    }

    public function setCloseTime(?\DateTimeImmutable $closeTime): static
    {
        $this->closeTime = $closeTime;

        return $this;
    }

    public function isClosed(): bool
    {
        return $this->isClosed;
    }

    public function setIsClosed(bool $isClosed): static
    {
        $this->isClosed = $isClosed;

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
        return $this->getDayLabel() . ' : ' . $this->getHoursLabel();
    }
}