<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_CATEGORY_NAME', fields: ['name'])]
#[ORM\UniqueConstraint(name: 'UNIQ_CATEGORY_SLUG', fields: ['slug'])]
#[UniqueEntity(fields: ['name'], message: 'Cette catégorie existe déjà.')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank(message: 'Le nom de la catégorie est obligatoire.')]
    #[Assert\Length(max: 80, maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $name = null;

    #[ORM\Column(length: 90)]
    private ?string $slug = null;

    /**
     * @var Collection<int, Speciality>
     */
    #[ORM\OneToMany(targetEntity: Speciality::class, mappedBy: 'category')]
    #[ORM\OrderBy(['name' => 'ASC'])]
    private Collection $specialities;

    public function __construct()
    {
        $this->specialities = new ArrayCollection();
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
        // Le slug suit automatiquement le nom
        $this->slug = (new AsciiSlugger())->slug($name)->lower()->toString();

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Speciality>
     */
    public function getSpecialities(): Collection
    {
        return $this->specialities;
    }

    public function addSpeciality(Speciality $speciality): static
    {
        if (!$this->specialities->contains($speciality)) {
            $this->specialities->add($speciality);
            $speciality->setCategory($this);
        }

        return $this;
    }

    public function removeSpeciality(Speciality $speciality): static
    {
        if ($this->specialities->removeElement($speciality)) {
            if ($speciality->getCategory() === $this) {
                $speciality->setCategory(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }
}