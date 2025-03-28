<?php

namespace App\Entity;

use App\Repository\SerieRepository;
use Composer\Semver\Constraint\Constraint;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SerieRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(columns: ['name', 'first_air_date'])]
#[UniqueEntity(fields: ['name', 'firstAirDate'], message: 'Cette série existe déja')]
class Serie
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 3, max: 15,
        minMessage: 'Ce nom est trop court. min: {{ limit }} caractères',
        maxMessage: 'Ce nom est trop long. max: {{ limit }} caractères'
    )]
    #[Assert\NotBlank(message: 'Remplis ce champs !!!!')]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $overview = null;

    #[ORM\Column(length: 255)]
    #[Assert\Choice(choices: ['canceled', 'ended', 'returning'], message: 'Ce choix n\'est pas correct')]
    private ?string $status = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Range(min: 0, max: 10, notInRangeMessage: 'Ce nombre doit etre compris entre 0 et 10')]
    private ?float $vote = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $popularity = null;

    #[ORM\Column(length: 255)]
    private ?string $genres = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $backdrop = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $poster = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\LessThan('today')]
    private ?\DateTimeInterface $firstAirDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\GreaterThan(propertyPath: 'firstAirDate')]
    #[Assert\When(
        expression: 'this.getStatus() == "ended" || this.getStatus() == "canceled"',
        constraints: [
            new Assert\NotBlank(message: 'Ce champ est obligatoire'),
        ]
    )]
    #[Assert\When(
        expression: 'this.getStatus() == "returning"',
        constraints: [
            new Assert\Blank(message: 'Ce champ ne doit pas être rempli si le status est à En cours'),
        ]
    )]
    private ?\DateTimeInterface $lastAirDate = null;

    #[ORM\Column(nullable: true)]
    private ?int $tmdbId = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreated = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateModified = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getOverview(): ?string
    {
        return $this->overview;
    }

    public function setOverview(?string $overview): static
    {
        $this->overview = $overview;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getVote(): ?float
    {
        return $this->vote;
    }

    public function setVote(?float $vote): static
    {
        $this->vote = $vote;

        return $this;
    }

    public function getPopularity(): ?string
    {
        return $this->popularity;
    }

    public function setPopularity(?string $popularity): static
    {
        $this->popularity = $popularity;

        return $this;
    }

    public function getGenres(): ?string
    {
        return $this->genres;
    }

    public function setGenres(string $genres): static
    {
        $this->genres = $genres;

        return $this;
    }

    public function getBackdrop(): ?string
    {
        return $this->backdrop;
    }

    public function setBackdrop(?string $backdrop): static
    {
        $this->backdrop = $backdrop;

        return $this;
    }

    public function getPoster(): ?string
    {
        return $this->poster;
    }

    public function setPoster(?string $poster): static
    {
        $this->poster = $poster;

        return $this;
    }

    public function getFirstAirDate(): ?\DateTimeInterface
    {
        return $this->firstAirDate;
    }

    public function setFirstAirDate(\DateTimeInterface $firstAirDate): static
    {
        $this->firstAirDate = $firstAirDate;

        return $this;
    }

    public function getLastAirDate(): ?\DateTimeInterface
    {
        return $this->lastAirDate;
    }

    public function setLastAirDate(?\DateTimeInterface $lastAirDate): static
    {
        $this->lastAirDate = $lastAirDate;

        return $this;
    }

    public function getTmdbId(): ?int
    {
        return $this->tmdbId;
    }

    public function setTmdbId(?int $tmdbId): static
    {
        $this->tmdbId = $tmdbId;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    #[ORM\PrePersist]
    public function setDateCreated(): static
    {
        $this->dateCreated = new \DateTime();

        return $this;
    }

    public function getDateModified(): ?\DateTimeInterface
    {
        return $this->dateModified;
    }

    #[ORM\PreUpdate]
    public function setDateModified(): static
    {
        $this->dateModified = new \DateTime();

        return $this;
    }
}
