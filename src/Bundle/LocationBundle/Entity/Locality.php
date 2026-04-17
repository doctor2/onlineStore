<?php

namespace App\Bundle\LocationBundle\Entity;

use App\Bundle\LocationBundle\Repository\LocalityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: LocalityRepository::class)]
#[ORM\Table(name: 'localities')]
class Locality
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['city'])]
    private int $id;

    #[ORM\Column(length: 255)]
    #[Groups(['city'])]
    private string $name;

    #[ORM\Column(length: 255)]
    private string $type;

    #[ORM\Column(nullable: true)]
    private ?int $population = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $district = null;

    #[ORM\Column(length: 255)]
    #[Groups(['city'])]
    private string $region;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getPopulation(): ?int
    {
        return $this->population;
    }

    public function setPopulation(?int $population): self
    {
        $this->population = $population;
        return $this;
    }

    public function getDistrict(): ?string
    {
        return $this->district;
    }

    public function setDistrict(?string $district): self
    {
        $this->district = $district;
        return $this;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;
        return $this;
    }
}
